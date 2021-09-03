<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use Closure;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\System\ComponentBuilderTrait;
use Triangulum\Yii\ModuleContainer\UI\Front\FrontBase;
use Triangulum\Yii\ModuleContainer\UI\Html\Label;
use Triangulum\Yii\ModuleContainer\UI\Html\Panel\PanelGrid;
use Yii;
use yii\base\Model;
use yii\data\BaseDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

abstract class ElementGrid extends ElementBase implements ComponentBuilderInterface
{
    use ComponentBuilderTrait;

    public string     $gridId             = '';
    public array      $gridActionColumn   = [];
    public array      $invisibleColumn    = [];
    public string     $caption            = '';
    public array      $captionOptions     = [];
    public bool       $borderLess         = true;
    public string     $mainContainerClass = '';
    public ?PanelGrid $panel              = null;
    public string     $gridWidgetClass    = GridView::class;

    protected ?BaseDataProvider $dataProvider  = null;
    protected ?Model            $searchModel   = null;
    protected string            $title         = '';
    protected array             $clickClassMap = [];
    protected array             $clickEventMap = [];
    protected array             $iconClassMap  = [
        'update'    => 'glyphicon glyphicon-pencil pointer margin-left-10',
        'view'      => 'glyphicon glyphicon-eye-open pointer margin-left-10',
        'duplicate' => 'glyphicon glyphicon-duplicate pointer margin-left-10',
        'delete'    => 'glyphicon glyphicon-trash pointer margin-left-10',
    ];

    public function init(): void
    {
        parent::init();

        if (empty($this->panel)) {
            $this->panel = Yii::$app->panelGrid;
        }
    }

    abstract protected function gridViewRowOptions(): Closure;

    abstract protected function gridViewColumnsConfig(Model $searchModel = null): array;

    public function render(): void
    {
        $this->gridPanelBegin($this->title);
        echo $this->widget();
        $this->gridPanelEnd();
    }

    public function widget(): string
    {
        return $this->gridWidgetClass::widget(
            $this->configure(
                $this->dataProvider,
                $this->searchModel
            )
        );
    }


    public function renderWithPjax(string $title = ''): void
    {
        $this->titleSet($title);
        $this->pjaxBegin();
        $this->render();
        $this->pjaxEnd();
    }

    public function renderByPjax(View $view, string $title = ''): void
    {
        $this->titleSet($title);
        $this->clickEventRegister($view);
        $this->pjaxBegin();
        $this->render();
        $this->pjaxEnd();
    }

    public function clickEventSet(ElementPopup $element, string $alias, bool $useGridRow = true): void
    {
        $this->clickEventMap[] = [
            'element'    => $element,
            'alias'      => $alias,
            'useGridRow' => $useGridRow,
            'clickEvent' => function ($view, $element, $alias, $useGridRow) {
                if ($element->isAllowed()) {
                    $element->registerPopup($view, $useGridRow);
                    $this->clickClassMap[$alias] = $element->clickClassPointer();
                }
            },
        ];
    }

    public function clickEventRegister(View $view): void
    {
        foreach ($this->clickEventMap as $event) {
            ($event['clickEvent'])(
                $view,
                $event['element'],
                $event['alias'],
                $event['useGridRow']
            );
        }
    }

    public function searchModelSet($searchModel): void
    {
        $this->searchModel = $searchModel;
    }

    public function dataProviderSet($dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }

    public function titleSet(string $title): void
    {
        $this->title = $title;
    }

    public function clickClassMapSet(array $clickClassMap): void
    {
        $this->clickClassMap = $clickClassMap;
    }

    public function setBorderLess(bool $borderLess): void
    {
        $this->borderLess = $borderLess;
    }

    protected function defineEditOrViewClickClass(): string
    {
        return $this->clickClassMap[FrontBase::ALIAS_EDITOR] ??
            ($this->clickClassMap[FrontBase::ALIAS_VIEWER] ?? '');
    }

    /**
     * @param ElementPopup[] $actionColumn
     * @param array          $iconClassMap
     */
    public function actionColumnSet(array $actionColumn, array $iconClassMap = []): void
    {
        foreach ($actionColumn as $element) {
            if ($element->isAllowed()) {
                $this->gridActionColumn[$element->extractAction()] = $element->clickClass();
            }
        }

        if (!empty($iconClassMap)) {
            $this->iconClassMap = array_merge($this->iconClassMap, $iconClassMap);
        }
    }

    public function pjaxBegin(array $config = []): void
    {
        Pjax::begin(array_merge($this->pjaxConfig(), $config));
    }

    protected function pjaxId(): string
    {
        return $this->gridId . '_pjax';
    }

    protected function pjaxConfig(): array
    {
        return [
            'id'              => $this->pjaxId(),
            'clientOptions'   => ['skipOuterContainers' => true],
            'enablePushState' => true,
        ];
    }

    public function pjaxEnd(): void
    {
        Pjax::end();
    }

    public function gridPanelBegin(string $title = null): void
    {
        echo $this->panel->begin($title, $this->route());
    }

    public function gridPanelEnd(): void
    {
        echo $this->panel->end();
    }

    protected function emptyColumn(string $clickClass = ''): array
    {
        return [
            'label'          => '',
            'contentOptions' => ['class' => $clickClass],
        ];
    }

    protected function loadActionColumn(): array
    {
        if (!$this->hasActionColumn()) {
            return ['visible' => false];
        }

        $buttonList = [];
        foreach ($this->gridActionColumn as $action => $clkClass) {
            if ($action === 'delete') {
                $gridId = $this->gridId;
                $buttonList[$action] = function ($url) use ($action, $gridId) {
                    $afterDeleteMsg = Label::warning('Deleted!    &nbsp; ');
                    $afterDeleteTitle = $this->loadModuleName();

                    return Html::a(
                        $this->icon($action, ''),
                        '#',
                        [
                            'onclick' => <<<JS
bootbox.confirm(
    "ARE YOU SURE YOU WANT TO <span class='btn-danger padding-lateral-5'>DELETE</span> THIS ITEM?",
    function (result) {
        if (result) {
            $.ajax('$url', {
                type: 'POST'
            }).done(function (data) {
                CORE.notifySuccess('$afterDeleteMsg', 'Deleting $afterDeleteTitle');
                CORE.refreshGrid('#$gridId');
            });
        }
    });
return false;
JS
                            ,
                        ]
                    );
                };
            } else {
                $buttonList[$action] = function ($url, $model) use ($action, $clkClass) {
                    return $this->icon($action, $clkClass);
                };
            }
        }

        if (empty($buttonList)) {
            return ['visible' => false];
        }

        return $this->actionColumnConfigure($buttonList);
    }

    protected function configure(BaseDataProvider $dataProvider, Model $searchModel = null): array
    {
        $config = [
            'id'           => $this->gridId,
            'dataProvider' => $dataProvider,
        ];

        if ($searchModel) {
            $config['filterModel'] = $searchModel;
        }

        if ($rowOption = $this->gridViewRowOptions()) {
            $config['rowOptions'] = $rowOption;
        }

        $config['columns'] = $this->gridViewColumnsConfig($searchModel);
        $config['tableOptions'] = [
            'class' => ['table table-bordered table-hover table-condensed borderless'],
        ];

        if ($this->borderLess) {
            $config['tableOptions']['class'][] = 'borderless';
        }

        $config['caption'] = $this->caption;
        $config['captionOptions'] = $this->captionOptions;

        return $config;
    }

    private function hasActionColumn(): bool
    {
        return !empty($this->gridActionColumn);
    }

    private function loadModuleName(): string
    {
        return !empty($this->moduleName) ? $this->moduleName : '';
    }

    private function icon(string $action, string $clkClass): string
    {
        $iconClass = $this->iconClassMap()[$action] ?? '';

        return ($iconClass) ?
            Html::tag(
                'span',
                '&nbsp;',
                [
                    'class' => [$iconClass, $clkClass],
                ]
            ) : '';
    }

    private function iconClassMap(): array
    {
        return $this->iconClassMap;
    }

    private function actionColumnConfigure(array $buttonList): array
    {
        $template = $this->generateActionTemplate(array_keys($buttonList));
        if (empty($template)) {
            return [];
        }

        return [
            'class'          => ActionColumn::class,
            'headerOptions'  => ['class' => 'action-column w80px'],
            'template'       => $template,
            'contentOptions' => ['class' => 'text-nowrap text-center'],
            'buttons'        => $buttonList,
        ];
    }

    private function generateActionTemplate(array $actionList = []): string
    {
        $actions = array_map(
            static function ($action) {
                return '{' . $action . '}';
            },
            $actionList
        );

        return implode('', $actions);
    }

    protected function isColumnVisible(string $attribute): bool
    {
        if (empty($this->invisibleColumn)) {
            return true;
        }

        return !in_array($attribute, $this->invisibleColumn, true);
    }

    public function setInvisibleColumn(array $invisibleColumn): void
    {
        $this->invisibleColumn = $invisibleColumn;
    }
}
