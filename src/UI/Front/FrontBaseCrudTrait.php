<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front;

trait FrontBaseCrudTrait
{
    public function init(): void
    {
        parent::init();
        $this->actionConfig = $this->frontConfigCrudDefault($this->gridClass);
    }
}
