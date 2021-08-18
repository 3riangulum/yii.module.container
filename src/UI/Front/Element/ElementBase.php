<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;

class ElementBase extends BaseObjectUI
{
    public string $tag         = '';
    public string $route       = '';
    public bool   $allowAction = false;

    protected string $pjaxId = '';

    public function extractAction(): string
    {
        $list = explode('/', $this->route());

        return array_pop($list);
    }

    protected function route(): string
    {
        return $this->route;
    }

    public function isAllowed(): bool
    {
        return $this->allowAction;
    }

}
