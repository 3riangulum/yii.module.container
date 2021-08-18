<?php

namespace Triangulum\Yii\ModuleContainer\UI\Access;

use Triangulum\Yii\ModuleContainer\ModuleContainerIdentityTrait;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Webmozart\Assert\Assert;
use Yii;

class RouterBase extends BaseObjectUI
{
    use ModuleContainerIdentityTrait;

    public const ID = 'Router';

    public const ACTION_INDEX     = 'index';
    public const ACTION_EDIT      = 'update';
    public const ACTION_CREATE    = 'create';
    public const ACTION_DELETE    = 'delete';
    public const ACTION_DUPLICATE = 'duplicate';
    public const ACTION_VIEW      = 'view';

    public string      $uri    = '';
    public ?AccessBase $access = null;

    public array  $actions   = [];
    private array $actionMap = [];

    public function init(): void
    {
        parent::init();
        $this->uri = $this->containerUri();
        Assert::isInstanceOf($this->access, AccessBase::class);

        foreach ($this->actions as $action) {
            $this->actionMap[$action] = $action;
        }
    }

    public function route(string $action, array $param = []): string
    {
        if (empty($rule = $this->rule($action))) {
            return '';
        }

        return Yii::$app
            ->getUrlManager()
            ->createUrl(
                array_merge([$rule], $param)
            );
    }

    public function rule(string $action): string
    {
        return $this->uri . '/' . $this->action($action);
    }

    public function tag(string $action): string
    {
        return str_replace(['/', '-'], ['_', '_'], $this->rule($action));
    }

    protected function action(string $action): string
    {
        if ('' === $action) {
            return '';
        }

        return $this->actionMap[$action] ?? '';
    }

    public function isAllowed(string $action): bool
    {
        $rule = $this->rule($action);
        if ('' === $rule) {
            return false;
        }

        return $this->access->can($rule);
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function export(string $action, string $reloadGridId = ''): array
    {
        return [
            'tag'          => $this->tag($action),
            'route'        => $this->route($action),
            'allowAction'  => $this->isAllowed($action),
            'reloadGridId' => $reloadGridId,
        ];
    }

}
