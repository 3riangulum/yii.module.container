<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

final class Time
{
    public static function datePicker(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form
            ->field($model, $field)
            ->widget(
                DatePicker::class,
                self::datePickerConfig($config)
            );
    }

    private static function datePickerConfig(array $config = []): array
    {
        $defaultConfig = [
            'options'       => [
                'placeholder'  => '',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ],
            'language'      => 'en',
            'layout'        => '{input}{remove}',
            'type'          => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose'      => true,
                'todayHighlight' => true,
                'format'         => 'yyyy-mm-dd',
            ],
        ];

        return ArrayHelper::merge($defaultConfig, $config);
    }

    private static function dateTimePickerConfig($conf = []): array
    {
        $def = [
            'layout'        => '{input}{remove}',
            'type'          => DateTimePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format'         => 'yyyy-mm-dd hh:ii:00',
                'minuteStep'     => 1,
                'todayHighlight' => true,
                'autoclose'      => true,
            ],
        ];

        return ArrayHelper::merge($def, $conf);
    }

    public static function dateTimePicker(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form
            ->field($model, $field)
            ->widget(
                DateTimePicker::class,
                self::dateTimePickerConfig($config)
            );
    }

    private static function dateTimePickerEndConfig(): array
    {
        return self::dateTimePickerConfig(['pluginOptions' => ['format' => 'yyyy-mm-dd hh:ii:59']]);
    }

    public static function dateTimePickerEnd(ActiveForm $form, $model, string $field, array $config = []): ActiveField
    {
        return $form
            ->field($model, $field)
            ->widget(
                DateTimePicker::class,
                self::dateTimePickerEndConfig()
            );
    }
}
