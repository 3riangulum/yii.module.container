<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\UI\Html\Button;
use Triangulum\Yii\ModuleContainer\UI\Html\Growl;
use Triangulum\Yii\ModuleContainer\UI\Html\Panel\PanelBase;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

class ElementPopup extends ElementBase implements ComponentBuilderInterface
{
    public string $actionDelete = '';
    public string $reloadGridId = '';

    public string     $dataMapper         = "['id']";
    public string     $mainContainerClass = 'core-popup-window';
    public string     $legend             = '';
    public ?bool      $pjaxLinkSelector   = false;
    public ?PanelBase $panel              = null;
    public array      $pk                 = [];

    protected bool $hasError = true;

    public function init(): void
    {
        parent::init();

        if (empty($this->panel)) {
            $this->panel = Yii::$app->panelBase;
        }
    }

    public static function builder(array $params): self
    {
        if (!isset($params['mainContainerClass'])) {
            $params['mainContainerClass'] = 'core-popup-window';
        }

        if (!isset($params['class'])) {
            $params['class'] = ElementPopup::class;
        }

        return Yii::createObject($params);
    }

    public function exportViewSuccessData(): array
    {
        return [
            'title'     => $this->title,
            'msg'       => 'The operation was successful',
            'popup'     => $this,
            'hideModal' => 1,
        ];
    }

    public function setHasError(bool $state): self
    {
        $this->hasError = $state;

        return $this;
    }

    public function setPk(array $pk): self
    {
        $this->pk = $pk;

        return $this;
    }

    public function pjaxBegin(bool $hasError = null): void
    {
        if ($hasError === null) {
            $hasError = $this->hasError;
        }

        Pjax::begin($this->pjaxConfig($hasError));
    }

    protected function pjaxConfig(bool $hasErrors): array
    {
        return [
            'id'            => $this->pjaxId(),
            'formSelector'  => $hasErrors ? false : null,
            'linkSelector'  => $this->pjaxLinkSelector,
            'clientOptions' => ['skipOuterContainers' => true],
        ];
    }

    protected function pjaxId(): string
    {
        if (empty($this->pjaxId)) {
            $this->pjaxId = $this->containerClass() . '_pjax' . time();
        }

        return $this->pjaxId;
    }

    public function pjaxEnd(): void
    {
        Pjax::end();
    }

    public function registerPopup(View $view, int $dataMapTableRecord = 0): void
    {
        if ($this->isAllowed()) {
            $this->registerPopupAsset($view);
            $this->containerCreate();
            $view->registerJs($this->popupObserverInit($dataMapTableRecord));
        }
    }

    protected function registerPopupAsset(View $view): void
    {
        ElementPopupAsset::register($view);
    }

    protected function containerClass(): string
    {
        return $this->tag . '_popup';
    }

    protected function containerCreate(): void
    {
        $this->popupEmbed(
            $this->containerClass(),
            $this->contentId()
        );
    }

    protected function popupEmbed(string $modalClass, string $contentId): void
    {
        Modal::begin([
            'options' => ['class' => $this->mainContainerClass . ' fade ' . $modalClass],
            'size'    => Modal::SIZE_LARGE,
            'header'  => '<span class="modal-title panel-heading"></span>',
        ]);
        echo '<div id="' . $contentId . '" class="popover-content"></div>';
        Modal::end();
    }

    protected function contentId(): string
    {
        return $this->containerClass() . '_content_';
    }

    protected function popupObserverInit(int $dataMapTableRecord = 0): string
    {
        $modalClass = $this->containerClass();
        $contentId = $this->contentId();
        $clickClass = $this->clickClass();
        $url = $this->defineUrl();
        $dataMapper = $this->dataMapper;

        return <<<JS
        
PopUp().init({
    clickClass: "$clickClass",
    modalClass: "$modalClass",
    contentId: "$contentId",
    baseUrl: "$url",
    dataMapperModeTr: $dataMapTableRecord,
    dataMapper: $dataMapper,
    doubleClick: false
})

JS;
    }

    protected function defineUrl(): string
    {
        return $this->route();
    }

    public function clickClass(): string
    {
        return $this->tag . '_clk';
    }

    public function registerPopupDoubleClick(View $view, int $dataMapTableRecord = 0): void
    {
        /* Edit popup */
        if ($this->isAllowed()) {
            $this->registerPopupAsset($view);
            $this->containerCreate();
            $view->registerJs($this->popupObserverInitDoubleClick($dataMapTableRecord));
        }
    }

    protected function popupObserverInitDoubleClick(int $dataMapTableRecord = 0): string
    {
        $modalClass = $this->containerClass();
        $contentId = $this->contentId();
        $clickClass = $this->clickClass();
        $url = $this->route();
        $dataMapper = $this->dataMapper;

        return <<<JS
        
PopUp().init({
    clickClass: "$clickClass",
    modalClass: "$modalClass",
    contentId: "$contentId",
    baseUrl: "$url",
    doubleClick: true,
    dataMapperModeTr: $dataMapTableRecord,
    dataMapper: $dataMapper
})

JS;
    }

    public function clickClassPointer(): string
    {
        return $this->clickClass() . ' pointer';
    }

    public function htmlButton(string $title = 'Create', string $btnClass = Button::CSS_BTN_SCCS): string
    {
        return $this->isAllowed() ?
            Html::tag(
                'span',
                $title,
                [
                    'class' => [
                        $btnClass,
                        $this->clickClassPointer(),
                    ],
                ]
            ) : '';
    }

    public function reloadGrid(View $view, string $growlTitle, string $growlMsg = 'The operation was successful', bool $success = true): void
    {
        if ($success) {
            Growl::growlOk($growlTitle, $growlMsg);
        } else {
            Growl::growlError($growlTitle, $growlMsg);
        }

        if (!empty($this->reloadGridId)) {
            $js = <<<JS

CORE.refreshGrid('#{$this->reloadGridId}');

JS;

            $view->registerJs($js, View::POS_LOAD);
        }
    }

    public function hideAndReloadGrid(int $hide = 0, View $view, string $growlTitle, string $growlMsg = 'The operation was successful', bool $success = true): void
    {
        if (!$hide) {
            return;
        }

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
        $this->reloadGrid($view, $growlTitle, $growlMsg, $success);
    }

    public function hideOnSuccess(int $success, View $view, string $growlTitle, string $growlMsg = 'The operation was successful'): void
    {
        if (!$success) {
            return;
        }

        Growl::growlOk($growlTitle, $growlMsg);

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
    }

    public function hideOnError(View $view, string $growlTitle, string $growlMsg = 'Error'): void
    {
        Growl::growlError($growlTitle, $growlMsg);

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
    }

    public function popupHideEditForm(): string
    {
        $class = $this->mainContainerClass;

        return <<<JS
 
$(".$class").modal('hide');

JS;
    }

    public function formGetBegin()
    {
        $config = $this->formConfig($this->route());
        $config['method'] = 'GET';

        return ActiveForm::begin($config);
    }

    public function formPostBegin()
    {
        $config = $this->formConfig($this->route());
        $config['method'] = 'POST';

        return ActiveForm::begin($config);
    }

    /**
     * @param string|null $action
     * @return Widget|ActiveForm
     */
    public function formBegin(string $action = null)
    {
        return ActiveForm::begin($this->formConfig($action));
    }

    public function formBeginMultiPart(string $action = null)
    {
        $config = $this->formConfig($action);
        $config['options']['enctype'] = 'multipart/form-data';

        return ActiveForm::begin($config);
    }

    protected function formConfig(string $action = null): array
    {
        $conf = [
            'id'      => $this->formId(),
            'options' => [
                'data-pjax' => 1,
                'class'     => $this->formClass(),
            ],
        ];

        if ($action) {
            $conf['action'] = $action;
        }

        return $conf;
    }

    public function formId(): string
    {
        return $this->containerClass() . '_form' . time();
    }

    public function formClass(): string
    {
        return $this->containerClass() . '_form';
    }

    public function formEnd(): void
    {
        ActiveForm::end();
    }

    protected function htmlButtonDelete(array $pKey = [], array $data = []): string
    {
        if (!$pKey || !$this->canDelete()) {
            return '';
        }

        $class = 'btn-danger btn-delete';
        $action = 'delete';
        $title = "<span class='$class padding-lateral-5 uppercase '>$action !</span>";

        return Html::a(
            $action,
            array_merge([$this->actionDelete], $pKey),
            [
                'class' => "btn $class btn-xs",
                'data'  => array_merge(
                    [
                        'confirm' => 'Confirm' . ' ' . $title . ' ' . 'Are you sure?',
                        'pjax'    => true,
                        'method'  => 'post',
                    ],
                    $data
                ),
            ]
        );
    }

    protected function canDelete(): bool
    {
        return !empty($this->actionDelete);
    }

    /**
     * @deprecated
     */
    public function panelBeginAdvanced(string $title, array $pKey = [], bool $showDelete = true, bool $encode = true, string $addon = ''): void
    {
        $this->panelBegin($title, $encode);
        if ($showDelete) {
            echo $this->htmlButtonDelete($pKey);
        }

        echo $addon;
    }

    /**
     * @deprecated
     */
    public function panelEndAdvanced(): void
    {
        $this->panelEnd();
    }

    public function panelBegin(string $title = '', bool $encode = true): void
    {
        echo $this->panel->begin(Html::encode(empty($title) ? $this->title : $title));

        if (!empty($this->pk)) {
            echo $this->htmlButtonDelete($this->pk);
        }
    }

    public function panelEnd(): void
    {
        echo $this->panel->end();
    }

    public function hasLegend(): bool
    {
        return !empty($this->getLegend());
    }

    private function getLegend(): string
    {
        return (string)$this->legend;
    }

    public function setLegend(string $legend): void
    {
        $this->legend = $legend;
    }

    public function legendBegin(): string
    {
        if (!$this->hasLegend()) {
            return '';
        }

        return <<< HTML
<fieldset>
    <legend><span class="label label-form">{$this->getLegend()}</span></legend>

HTML;
    }

    public function legendEnd(): string
    {
        if (!$this->hasLegend()) {
            return '';
        }

        return <<< HTML

</fieldset>
HTML;
    }
}
