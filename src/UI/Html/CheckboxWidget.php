<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use bookin\aws\checkbox\AwesomeCheckboxAsset;
use bookin\aws\checkbox\FontAwesomeAsset;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class CheckboxWidget extends InputWidget
{
    public const  TYPE_CHECKBOX = 'checkbox';
    public const  TYPE_RADIO    = 'radio';

    public const STYLE_DEFAULT = '';
    public const STYLE_PRIMARY = 'primary';
    public const STYLE_SUCCESS = 'success';
    public const STYLE_INFO    = 'info';
    public const STYLE_WARNING = 'warning';
    public const STYLE_DANGER  = 'danger';
    public const STYLE_CIRCLE  = 'circle';
    public const STYLE_INLINE  = 'inline';

    public array  $checked        = [];
    public string $type           = self::TYPE_CHECKBOX;
    public string $style          = self::STYLE_DEFAULT;
    public array  $list           = [];
    public array  $wrapperOptions = [];

    public function run(): string
    {
        AwesomeCheckboxAsset::register($this->getView());

        if (!empty($this->list) && is_array($this->list)) {
            return $this->renderList();
        }

        return $this->renderItem();
    }

    protected function renderItem(): string
    {
        $html = [];
        $html[] = Html::beginTag('div', array_merge(['class' => $this->getClass()], $this->wrapperOptions));
        $label = $this->labelContent;
        $html[] = $this->input;
        if ($label) {
            $html[] = Html::tag('label', $label, ['for' => $this->labelId]);
        }
        $html [] = Html::endTag('div');

        return implode('', $html);
    }

    protected function renderList()
    {
        $listAction = $this->type . 'List';

        $this->options['item'] = function ($index, $label, $name, $checked, $value) {
            $optionIndex = (int)($this->options['index'] ?? 0);
            if ($optionIndex) {
                $index = $optionIndex . '-' . $value;
            }

            $action = $this->type;
            $id = strtolower(
                $index . '-' . str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name)
            );
            $html = [];
            $html[] = Html::beginTag('div', ['class' => $this->getClass()]);
            $html[] = Html::$action($name, $checked, ['label' => null, 'value' => $value, 'id' => $id]);
            $html[] = Html::tag('label', $label, ['for' => $id]);
            $html[] = Html::endTag('div');

            return implode(' ', $html);
        };

        if ($this->hasModel()) {
            $listAction = 'active' . ucfirst($listAction);
            $input = Html::$listAction($this->model, $this->attribute, $this->list, $this->options);
        } else {
            $input = Html::$listAction($this->name, $this->checked, $this->list, $this->options);
        }

        return $input;
    }

    protected function getLabelContent(): string
    {
        $label = array_key_exists('label', $this->options) ? $this->options['label'] : '';
        if ($this->hasModel() && empty($label)) {
            $label = Html::encode($this->model->getAttributeLabel(Html::getAttributeName($this->attribute)));
        }
        $this->options['label'] = null;

        return $label;
    }

    protected function getLabelId(): string
    {
        $id = $this->id;
        if ($this->hasModel() && !array_key_exists('id', $this->options)) {
            $id = Html::getInputId($this->model, $this->attribute);
        } elseif (isset($this->options['id'])) {
            $id = $this->options['id'];
        }

        return $id;
    }

    protected function getInput(): string
    {
        $inputType = ucfirst($this->type);
        if ($this->hasModel()) {
            $inputType = 'active' . $inputType;
            $input = Html::$inputType($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::$inputType($this->name, $this->checked, $this->options);
        }

        return $input;
    }

    protected function getClass(): string
    {
        $class = [];
        $class[] = $this->type;
        if (!empty($this->style)) {
            if (is_array($this->style)) {
                $class = array_merge($class, array_map(function ($item) {
                    return $this->type . '-' . $item;
                }, $this->style));
            } else {
                $class[] = $this->type . '-' . $this->style;
            }
        }
        if (isset($this->wrapperOptions['class']) && !empty($this->wrapperOptions['class'])) {
            $class = array_merge($class, preg_split('/\s+/', $this->wrapperOptions['class']));
        }

        return implode(' ', $class);
    }
}
