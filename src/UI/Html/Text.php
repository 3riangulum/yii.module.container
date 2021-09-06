<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use yii\helpers\Html;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

final class Text
{
    public static function input(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form->field($model, $field)->textInput($config);
    }

    public static function hiddenInput(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form->field($model, $field)->hiddenInput($config)->label(false);
    }

    public static function fakeInput(string $title, string $value, string $class = '', $readonly = true): string
    {
        $input = Html::tag(
            'span',
            $value,
            [
                'class'    => ['form-control', $class],
                'readonly' => $readonly,
            ]
        );

        return <<< HTML
<div class="form-group">
    <label class="control-label">$title</label>
    $input
</div>
HTML;
    }

    public static function area(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form
            ->field($model, $field)
            ->textarea(
                array_merge(
                    [
                        'maxlength' => true,
                        'rows'      => 4,
                    ],
                    $config
                )
            );
    }
}
