<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\AutoComplete;

use kartik\select2\Select2;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderTrait;

class AutoCompleteSelectGrid extends AutoCompleteSelectBase implements ComponentBuilderInterface
{
    use ComponentBuilderTrait;

    public function widget(): string
    {
        $cfg = $this->getWidgetConfig();
        $cfg['model'] = $this->model;
        $cfg['attribute'] = $this->attribute;

        return Select2::widget($cfg);
    }
}
