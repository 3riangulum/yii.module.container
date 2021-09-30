<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use kartik\switchinput\SwitchInput;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

final class Switcher
{
    public static function input(ActiveForm $form, $model, string $field, string $onColor = 'success', $offColor = 'danger', $onText = ' &nbsp; YES &nbsp; ', $offText = ' &nbsp; NO &nbsp; '): ActiveField
    {
        return $form->field($model, $field)->widget(SwitchInput::class, [
            'type'          => SwitchInput::CHECKBOX,
            'tristate'      => false,
            'pluginOptions' => [
                'size'     => 'mini',
                'onColor'  => $onColor,
                'offColor' => $offColor,
                'onText'   => $onText,
                'offText'  => $offText,
            ],
        ]);
    }

    public static function status(ActiveForm $form, $model, string $field, array $txt = ['ON', 'OFF']): ActiveField
    {
        return self::input(
            $form,
            $model,
            $field,
            'success',
            'danger',
            $txt[0],
            $txt[1]
        );
    }

    public static function statusInvertedColor(ActiveForm $form, $model, string $field, array $txt = ['ON', 'OFF']): ActiveField
    {
        return self::input(
            $form,
            $model,
            $field,
            'danger',
            'success',
            $txt[0],
            $txt[1]
        );
    }
}
