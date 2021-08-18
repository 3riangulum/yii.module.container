<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use yii\widgets\ActiveForm;

final class CheckBox
{
    private const CLASS_SELECTABLE = 'selectable_container';

    public static function element(string $name, array $options): string
    {
        return CheckboxWidget::widget(
            [
                'name'    => $name,
                'style'   => CheckboxWidget::STYLE_DEFAULT,
                'options' => $options,
            ]
        );
    }

    public static function listInline(string $name, array $list, array $checked = [], bool $slim = true): string
    {
        $classList = ['checkbox-inline'];
        if ($slim) {
            $classList[] = 'checkbox-inline-list-slim';
        } else {
            $classList[] = 'checkbox-inline-list-wide';
        }

        return CheckboxWidget::widget(
            [
                'name'           => $name,
                'list'           => $list,
                'checked'        => $checked,
                'style'          => CheckboxWidget::STYLE_DEFAULT,
                'options'        => ['class' => 'checkbox-list-container'],
                'wrapperOptions' => [
                    'class' => implode(' ', $classList),
                ],
            ]
        );
    }

    private static function checkBoxListSelectableBegin(): string
    {
        return '<div class="' . self::CLASS_SELECTABLE . '">';
    }

    private static function checkBoxListSelectableEnd(): string
    {
        return '</div>';
    }

    public static function formListInlineSelectable(
        ActiveForm $form,
        $model,
        string $field,
        array $list,
        bool $slim = true,
        $label = null,
        $index = null
    ): void {
        if (empty($list)) {
            echo Label::danger("ERROR: empty list for $field field");

            return;
        }

        echo self::checkBoxListSelectableBegin();
        $checkBox = $form->field($model, $field)
            ->widget(
                CheckboxWidget::class,
                [
                    'style'          => CheckboxWidget::STYLE_DEFAULT,
                    'options'        => ['class' => 'checkbox-list-container', 'index' => $index],
                    'list'           => $list,
                    'wrapperOptions' => [
                        'class' => 'checkbox-inline checkbox-inline-list' . ($slim ? '-slim' : ''),
                    ],
                ]
            );

        if (null !== $label) {
            $checkBox->label($label, ['class' => 'control-label text-nowrap']);
        }

        echo $checkBox;
        echo self::checkBoxListSelectableEnd();
    }

    public static function formListVerticalSelectable(
        ActiveForm $form,
        $model,
        string $field,
        array $list,
        string $style = CheckboxWidget::STYLE_DEFAULT,
        $label = null
    ): void {
        if (empty($list)) {
            echo Label::danger("ERROR: empty list for $field field");

            return;
        }

        echo self::checkBoxListSelectableBegin();
        $checkBox = $form->field($model, $field)
            ->widget(
                CheckboxWidget::class,
                self::loadOption(
                    false,
                    [
                        'type'           => CheckboxWidget::TYPE_CHECKBOX,
                        'style'          => $style,
                        'list'           => $list,
                        'options'        => [
                            'label' => '&nbsp;',
                            'class' => 'checkbox-list-container',
                        ],
                        'wrapperOptions' => [
                            'class' => 'checkbox-vertical',
                        ],
                    ]
                )
            );

        if (null !== $label) {
            $checkBox->label($label, ['class' => 'control-label text-nowrap  text-center']);
        }

        echo $checkBox;
        echo self::checkBoxListSelectableEnd();
    }

    public static function formElement(
        ActiveForm $form,
        $model,
        string $field,
        string $style = null,
        bool $disabled = false
    ): string {
        return $form->field($model, $field)
            ->widget(
                CheckboxWidget::class,
                self::loadOption(
                    false,
                    [
                        'type'    => CheckboxWidget::TYPE_CHECKBOX,
                        'style'   => $style ?? CheckboxWidget::STYLE_DEFAULT,
                        'options' => [
                            'disabled' => $disabled,
                        ],
                    ]
                )
            )->label(false);
    }

    public static function loadOption($circle = true, $data = []): array
    {
        $defaults = [
            'type'  => CheckboxWidget::TYPE_CHECKBOX,
            'style' => CheckboxWidget::STYLE_DEFAULT,
        ];

        if ($circle) {
            $defaults['style'] = CheckboxWidget::STYLE_CIRCLE;
        }

        if ($data) {
            $defaults = array_merge($defaults, $data);
        }

        return $defaults;
    }
}
