<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use yii\base\InvalidConfigException;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

final class Radio
{
    public static function inlineForm(ActiveForm $form, $model, string $field, array $list): ActiveField
    {
        if (empty($list)) {
            throw new InvalidConfigException('List is empty');
        }

        return $form->field($model, $field)
            ->widget(CheckboxWidget::class, [
                'type'           => CheckboxWidget::TYPE_RADIO,
                'style'          => CheckboxWidget::STYLE_DEFAULT,
                'list'           => $list,
                'options'        => ['class' => 'checkbox-list-container'],
                'wrapperOptions' => [
                    'class' => 'checkbox-inline checkbox-inline-list-slim uppercase',
                ],
            ]);
    }

    /**
     * @throws InvalidConfigException
     */
    public static function verticalForm(
        ActiveForm $form,
        $model,
        string $field,
        array $list,
        string $id = null,
        string $style = CheckboxWidget::STYLE_DEFAULT
    ): ActiveField {
        if (empty($list)) {
            throw new InvalidConfigException('List is empty');
        }

        return $form->field($model, $field)
            ->widget(CheckboxWidget::class, [
                'type'           => CheckboxWidget::TYPE_RADIO,
                'style'          => $style,
                'list'           => $list,
                'options'        => [
                    'class' => 'checkbox-list-container',
                ],
                'id'             => $id,
                'wrapperOptions' => [
                    'class' => 'checkbox-vertical uppercase',
                ],
            ]);
    }
}
