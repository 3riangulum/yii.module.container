<?php

namespace Triangulum\Yii\ModuleContainer\UI\Access;

use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderTrait;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Webmozart\Assert\Assert;
use Yii;
use yii\caching\CacheInterface;
use yii\db\Connection;
use yii\db\Query;

final class RbacInfo extends BaseObjectUI implements ComponentBuilderInterface
{
    use ComponentBuilderTrait;

    public const ID = 'RbacInfo';

    public string $aliasCache      = 'cache';
    public string $aliasDb         = 'db';
    public int $cacheDuration   = 60;
    public string $itemTable       = '{{%auth_item}}';
    public string $itemChildTable  = '{{%auth_item_child}}';
    public string $assignmentTable = '{{%auth_assignment}}';
    public string $ruleTable       = '{{%auth_rule}}';

    public ?Connection $db    = null;
    private ?CacheInterface $cache = null;

    public function init(): void
    {
        parent::init();
        Assert::notEmpty($this->aliasCache);
        Assert::notEmpty($this->aliasDb);

        $this->db = Yii::$app->get($this->aliasDb);
        $this->cache = Yii::$app->get($this->aliasCache);
    }

    public function listItemChildRoles(string $child): array
    {
        return $this->mapItemChildParents()[$child] ?? [];
    }

    public function listUserRoleMapDetailed(int $userId): array
    {
        $assignments = $this->getAssignments();
        $userItems = $assignments[$userId] ?? [];
        $items = $this->getItems();
        $itemsChilds = $this->getItemsChilds();

        $map = [];

        foreach ($userItems as $roleAlias) {
            if (isset($items[$roleAlias]) && isset($itemsChilds[$roleAlias])) {
                $map[$items[$roleAlias]] = $itemsChilds[$roleAlias];
            }
        }

        return $map;
    }

    public function listUserRoles(int $userId): array
    {
        return $this->mapUserRoles()[$userId] ?? [];
    }

    public function searchUidByRoleDescription(string $roleDescription = null): array
    {
        return $this->searchUidByRoleName(
            $this->searchNameByRoleDescription($roleDescription)
        );
    }

    private function getAssignments(): array
    {
        return $this->cache->getOrSet(
            [__METHOD__],
            function () {
                $query = (new Query())
                    ->from($this->assignmentTable)
                    ->orderBy(['item_name' => SORT_ASC]);

                $assignments = [];
                foreach ($query->all($this->db) as $row) {
                    $assignments[$row['user_id']][] = $row['item_name'];
                }

                return $assignments;
            },
            $this->cacheDuration
        );
    }

    private function getItems(): array
    {
        return $this->cache->getOrSet(
            [__METHOD__],
            function () {
                $items = [];
                $query = (new Query())->from($this->itemTable);
                foreach ($query->all($this->db) as $row) {
                    $items[$row['name']] = $row['description'];
                }

                return $items;
            },
            $this->cacheDuration
        );
    }

    private function getItemsChilds(): array
    {
        return $this->cache->getOrSet(
            [__METHOD__],
            function () {
                $query = (new Query())->from($this->itemChildTable);
                $childs = [];
                foreach ($query->all($this->db) as $row) {
                    $childs[$row['parent']][] = $row['child'];
                }

                return $childs;
            },
            $this->cacheDuration
        );
    }

    private function searchNameByRoleDescription(string $roleDescription = null): array
    {
        if (empty($roleDescription)) {
            return [];
        }

        return (new Query())->from($this->itemTable)
            ->select('name')
            ->where(['like', 'description', $roleDescription])
            ->createCommand($this->db)
            ->queryColumn();
    }

    private function searchUidByRoleName(array $roleNameList = []): array
    {
        if (empty($roleNameList)) {
            return [];
        }

        return (new Query())
            ->from($this->assignmentTable)
            ->select('user_id')
            ->where(['IN', 'item_name', $roleNameList])
            ->createCommand($this->db)
            ->queryColumn();
    }

    private function mapItemChildParents(): array
    {
        return $this->cache->getOrSet(
            [__METHOD__],
            function () {
                $ret = [];
                $list = (new Query())->from($this->itemChildTable)->createCommand($this->db)->queryAll();
                foreach ($list as $row) {
                    $ret[$row['child']][] = $row['parent'];
                }

                return $ret;
            },
            $this->cacheDuration
        );
    }

    private function mapUserRoles(): array
    {
        return $this->cache->getOrSet(
            [__METHOD__],
            function () {
                $assignments = $this->getAssignments();
                $items = $this->getItems();

                $map = [];
                foreach ($assignments as $userId => $userItems) {
                    foreach ($userItems as $roleAlias) {
                        $map[$userId][] = $items[$roleAlias];
                    }
                }

                return $map;
            },
            $this->cacheDuration
        );
    }
}
