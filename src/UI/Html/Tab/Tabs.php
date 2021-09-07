<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\Tab;

use ReflectionClass;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderTrait;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Triangulum\Yii\ModuleContainer\UI\Front\Element\ElementPopup;
use Triangulum\Yii\ModuleContainer\UI\Html\Button;
use Yii;

class Tabs extends BaseObjectUI implements ComponentBuilderInterface
{
    use ComponentBuilderTrait;

    public string $templateDir = '';
    public string $viewNav     = '_nav.php';
    public string $viewContent = '_content.php';

    public array         $config = [];
    public string        $alias  = '';
    public string        $formId = '';
    public ?ElementPopup $popup  = null;

    public function init(): void
    {
        parent::init();
        if (empty($this->templateDir)) {
            $this->templateDir = $this->defaultTemplatePath();
        }
    }

    public function render(): void
    {
        $this->popup->pjaxBegin();
        $this->popup->panelBegin();
        $this->renderFormTabs();
        $this->popup->panelEnd();
        $this->popup->pjaxEnd();
    }

    public function renderFormTabs(): void
    {
        $this->popup->formBegin();
        $this->renderTabs();
        echo Button::submitBottom();
        $this->popup->formEnd();
        $this->jsRegisterErrorHandling($this->popup->formId());
    }

    public function renderTabs(): void
    {
        echo '<div id="' . $this->getContainerClass() . '">';
        $this->nav();
        $this->content();
        echo '</div>';
    }

    public function nav(): void
    {
        echo $this->renderContent(
            $this->templateDir . $this->viewNav,
            [
                'config' => $this->config,
                'alias'  => $this->getAlias(),
            ]
        );
    }

    public function content(): void
    {
        echo $this->renderContent(
            $this->templateDir . $this->viewContent,
            [
                'config' => $this->config,
                'alias'  => $this->getAlias(),
            ]
        );
    }

    public function jsRegisterErrorHandling(string $formId): void
    {
        $id = $this->getContainerClass();
        Yii::$app->getView()->registerJs(
            <<<JS
var TabMarks = {
    tabMarkHasError: function (navTabClass) {
        $(navTabClass).addClass('flash-bg-red');
        setTimeout(function () {
            TabMarks.tabMarkHasNoError(navTabClass);
        }, 1000);
    },
    tabMarkHasNoError: function (navTabClass) {
        $(navTabClass).removeClass('flash-bg-red');
    }
}

$('#$formId')
    .on('afterValidate', function (event, messages, errorAttributes) {
        $('#$id .tab-content .tab-pane').each(function () {
            let errored = $(this).find('.has-error');
            if (errored.length) {
                let contentId = errored.parents('.tab-pane').attr('id');
                TabMarks.tabMarkHasError('#$id .nav-tab-' + contentId);
            }
        });
});
JS
            ,
            yii\web\View::POS_LOAD
        );
    }

    private function getAlias(): string
    {
        return $this->alias . '_tabs';
    }

    private function getContainerClass(): string
    {
        return 'container_' . $this->getAlias();
    }

    private function defaultTemplatePath(): string
    {
        return dirname((new ReflectionClass($this))->getFileName()) . '/views/';
    }

    private function renderContent(string $view, array $params = []): string
    {
        return Yii::$app->getView()->renderFile($view, $params);
    }
}
