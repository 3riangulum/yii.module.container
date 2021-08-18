<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\AutoComplete;

use kartik\select2\Select2;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use yii\base\Model;
use yii\web\JsExpression;

abstract class AutoCompleteSelectBase extends BaseObjectUI
{
    public ?Model  $model     = null;
    public ?string $attribute = null;

    protected array $options = [
        'placeholder' => '',
    ];

    /**
     * String then pluginOptions.multiple = false
     * Array then pluginOptions.multiple = true
     * @var null|string|array
     */
    protected $initValueText = null;

    protected array $pluginOptions = [
        'allowClear'         => true,
        'multiple'           => false,
        'minimumInputLength' => 3,
        'maximumInputLength' => 200,
    ];

    protected array $ajax = [
        'delay'    => 250,
        'dataType' => 'json',
    ];

    protected array $pluginEvents = [];

    public function init(): void
    {
        parent::init();

        $this->setPluginOptions(
            'escapeMarkup',
            new JsExpression('function (markup) { return markup; }')
        );

        $this->setAjaxOption(
            'data',
            new JsExpression('function(params) { return {term:params.term}; }')
        );
    }

    abstract public function widget();

    public function initValueText($data = null): void
    {
        $this->initValueText = $data;
    }

    protected function getWidgetConfig(): array
    {
        $config = [
            'theme'         => Select2::THEME_BOOTSTRAP,
            'language'      => 'en',
            'showToggleAll' => false,
            'maintainOrder' => true,
            'options'       => $this->getOptions(),
            'pluginOptions' => $this->getPluginOptions(),
        ];

        if (!empty($this->initValueText)) {
            $config['initValueText'] = $this->initValueText;
        }

        if ($events = $this->getEvents()) {
            $config['pluginEvents'] = $events;
        }

        return $config;
    }

    public function setOptions(string $index, $value): AutoCompleteSelectBase
    {
        $this->options[$index] = $value;

        return $this;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    public function setPluginOptions(string $index, $value): AutoCompleteSelectBase
    {
        $this->pluginOptions[$index] = $value;

        return $this;
    }

    protected function getPluginOptions(): array
    {
        $opt = $this->pluginOptions;
        $opt['ajax'] = $this->getAjaxOption();

        return $opt;
    }

    public function allowMultiple(): AutoCompleteSelectBase
    {
        return $this->setPluginOptions('multiple', true);
    }

    public function setAjaxOption(string $index, $value): AutoCompleteSelectBase
    {
        $this->ajax[$index] = $value;

        return $this;
    }

    protected function getAjaxOption(): array
    {
        return $this->ajax;
    }

    public function setAjaxRoute(string $route): AutoCompleteSelectBase
    {
        return $this->setAjaxOption('url', $route);
    }

    public function setEvents(array $eventList): AutoCompleteSelectBase
    {
        $this->pluginEvents = $eventList;

        return $this;
    }

    public function addEvent(string $name, string $jsHandler): void
    {
        $this->pluginEvents[$name] = $jsHandler;
    }

    protected function getEvents(): array
    {
        return $this->pluginEvents;
    }
}
