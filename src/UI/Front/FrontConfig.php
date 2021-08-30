<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front;

use Triangulum\Yii\ModuleContainer\System\ComponentBuilderTrait;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use yii\helpers\BaseInflector;
use yii\helpers\Inflector;

final class FrontConfig extends BaseObjectUI
{
    use ComponentBuilderTrait;

    public const ID = 'FrontConfig';

    public string $gridId       = 'grid';
    public string $gridClass    = '';
    public string $reloadGridId = '';
    public string $delete       = '';


    public string $dataMapper   = "['id']";

    public ?RouterBase $router = null;

    private array $actionConfig = [];
    private ?string $gridTag      = null;

    private string $deleteAction = '';

    public function init(): void
    {
        parent::init();

        if (!empty($this->delete) && $this->router->isAllowed($this->delete)) {
            $this->deleteAction = $this->router->route($this->delete);
        }
    }

    public function mix(self $frontConfig): self
    {
        foreach ($frontConfig->export() as $alias => $config) {
            $this->actionConfig[$alias] = $config;
        }

        return $this;
    }

    public function buildGrid(string $alias, string $action): self
    {
        $this->actionConfig[$alias] = [
            'route'       => $this->router->route($action),
            'allowAction' => $this->router->isAllowed($action),
            'gridId'      => $this->gridTag(),
            'class'       => $this->gridClass,
        ];

        return $this;
    }

    public function buildPopup(string $alias, string $action, bool $gridRefresh = true, bool $delete = true, string $dataMapper = "['id']"): self
    {
        $this->actionConfig[$alias] = [
            'tag'          => $this->tagByAction($action),
            'route'        => $this->router->route($action),
            'allowAction'  => $this->router->isAllowed($action),
            'reloadGridId' => $gridRefresh ? $this->gridTag() : '',
            'actionDelete' => $delete ? $this->deleteAction : '',
            'dataMapper'   => $dataMapper,
        ];

        return $this;
    }

    public function buildDelete(): self
    {
        if (!empty($this->delete)) {
            $this->buildPopup($this->delete, $this->delete);
        }

        return $this;
    }

    public function tagFilter(string $alias): string
    {
        return BaseInflector::underscore(
            Inflector::variablize($alias),
            '_'
        );
    }

    public function tagByAction(string $action): string
    {
        return $this->tagFilter(
            $this->router->rule($action)
        );
    }

    protected function gridTag(): string
    {
        if (null === $this->gridTag) {
            $this->gridTag = $this->tagFilter(
                $this->router->uri() . '_' . $this->gridId
            );
        }

        return $this->gridTag;
    }

    protected function gridId(string $gridId = ''): string
    {
        return $this->tagFilter($this->router->uri()) . '_' . ($gridId ?? '');
    }

    public function export(): array
    {
        return $this->actionConfig;
    }
}
