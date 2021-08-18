<?php

namespace Triangulum\Yii\ModuleContainer\UI\Menu;

use Triangulum\Yii\ModuleContainer\ModuleContainerIdentityTrait;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;

class MenuItem extends BaseObjectUI
{
    use ModuleContainerIdentityTrait;

    public const ID = 'Menu';

    public string      $title  = '';
    public string      $action = RouterBase::ACTION_INDEX;
    public ?RouterBase $router = null;

    public function init(): void
    {
        parent::init();
        $this->router = $this->loadRouter();
    }

    public function export(): array
    {
        return [
            'label'   => $this->title,
            'url'     => $this->router->route($this->action),
            'alias'   => [
                trim($this->router->uri(), '/'),
            ],
            'visible' => $this->router->isAllowed($this->action),
            'icon'    => ' ',
        ];
    }

    public function isAllowed(): bool
    {
        return $this->router->isAllowed($this->action);
    }
}
