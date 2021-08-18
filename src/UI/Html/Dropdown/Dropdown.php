<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\Dropdown;

use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

final class Dropdown
{
    public static function simple(ActiveForm $form, $model, string $field, array $items, $options = []): ActiveField
    {
        return $form
            ->field($model, $field)
            ->dropDownList($items, $options);
    }

    public static function element($model, string $field, array $items, string $selected = null): string
    {
        return Select2::widget([
            'model'         => $model,
            'attribute'     => $field,
            'theme'         => Select2::THEME_BOOTSTRAP,
            'showToggleAll' => false,
            'initValueText' => $selected,
            'options'       => [
                'placeholder' => '',
            ],
            'hideSearch'    => true,
            'pluginOptions' => [
                'allowClear'   => true,
                'data'         => $items,
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            ],
        ]);
    }

    public static function formElement(ActiveForm $form, $model, string $attribute, FilterDropDown $filter): ActiveField
    {
        $config = self::config();
        $config['initValueText'] = $filter->label($model->{$attribute});
        $config['pluginOptions']['data'] = $filter->items();

        return $form
            ->field($model, $attribute)
            ->widget(Select2::class, $config);
    }

    public static function formElementMulti(ActiveForm $form, $model, string $attribute, FilterDropDown $filter): ActiveField
    {
        $items = $filter->items();
        $selected = $model->{$attribute};
        $selectedValues = [];
        if (null !== $selected) {
            foreach ($items as $item) {
                if (!in_array($item['id'], $selected)) {
                    continue;
                }

                $selectedValues[] = $item['text'];
            }
        }

        $config = self::config();
        $config['initValueText'] = $selectedValues;
        $config['pluginOptions']['data'] = $items;
        $config['pluginOptions']['multiple'] = true;

        return $form
            ->field($model, $attribute)
            ->widget(Select2::class, $config);
    }

    private static function config(): array
    {
        return [
            'theme'         => Select2::THEME_BOOTSTRAP,
            'language'      => 'en',
            'showToggleAll' => false,
            'maintainOrder' => true,
            'options'       => ['placeholder' => ''],
            'hideSearch'    => false,
            'pluginOptions' => [
                'multiple'     => false,
                'allowClear'   => true,
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'title'        => '',
            ],
        ];
    }
}
