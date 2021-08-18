<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\AutoComplete;

use kartik\select2\Select2;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class AutoCompleteSelectForm extends AutoCompleteSelectBase
{
    public ?ActiveForm $form = null;

    public function widget(): ActiveField
    {
        return $this->form
            ->field($this->model, $this->attribute)
            ->widget(
                Select2::class,
                $this->getWidgetConfig()
            );
    }
}
