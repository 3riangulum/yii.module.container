<?php

namespace Triangulum\Yii\ModuleContainer\UI\Access;

use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Yii;

class AccessBase extends BaseObjectUI
{
    public string $cacheAlias    = 'cache';
    public int    $cacheDuration = 3600;
    public string $roleRoot      = 'role-root';

    private int $uid = 0;

    public function init(): void
    {
        $this->uid = $this->defineUid();
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function hasRoleRoot(): bool
    {
        return Yii::$app->user->can($this->roleRoot);
    }

    public function can(string $rule): bool
    {
        if (empty($this->uid)) {
            return false;
        }

        return isset($this->listUserPermissions()[$rule]);
    }

    private function listUserPermissions(): array
    {
        if (empty($this->uid)) {
            return [];
        }

        return Yii::$app->get($this->cacheAlias)->getOrSet(
            [__METHOD__, $this->uid],
            function () {
                return array_fill_keys(
                    array_keys(
                        Yii::$app->authManager->getPermissionsByUser($this->uid)
                    ),
                    0
                );
            },
            $this->cacheDuration
        );
    }

    private function defineUid(): int
    {
        if (empty(Yii::$app->user->getIdentity())) {
            return 0;
        }

        return Yii::$app->user->getIdentity()->getId();
    }

}
