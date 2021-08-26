<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front;

use Triangulum\Yii\ModuleContainer\ModuleContainerIdentityTrait;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Triangulum\Yii\ModuleContainer\UI\Front\Element\ElementGrid;
use Triangulum\Yii\ModuleContainer\UI\Front\Element\ElementPopup;
use Triangulum\Yii\ModuleContainer\UI\Html\AutoComplete\AutoCompleteSelectGrid;
use yii\base\Model;

class FrontBase extends BaseObjectUI
{
    use ModuleContainerIdentityTrait;

    public const ID = 'Front';

    public const ALIAS_GRID       = 'grid';
    public const ALIAS_EDITOR     = 'editor';
    public const ALIAS_CREATOR    = 'creator';
    public const ALIAS_DUPLICATOR = 'duplicator';
    public const ALIAS_ERASER     = 'eraser';
    public const ALIAS_VIEWER     = 'viewer';

    public ?string $gridClass       = null;
    public array   $htmlConfig      = [];
    public string  $aliasGrid       = self::ALIAS_GRID;
    public string  $aliasEditor     = self::ALIAS_EDITOR;
    public string  $aliasCreator    = self::ALIAS_CREATOR;
    public string  $aliasDuplicator = self::ALIAS_DUPLICATOR;
    public string  $aliasEraser     = self::ALIAS_ERASER;
    public string  $aliasViewer     = self::ALIAS_VIEWER;

    protected ?RouterBase $router       = null;
    protected array       $actionConfig = [];

    public function init(): void
    {
        parent::init();
        $this->router = $this->loadRouter();
    }

    protected function gridTag(): string
    {
        return $this->router->tag(RouterBase::ACTION_INDEX);
    }

    protected function gridId(): string
    {
        return $this->gridTag() . '_' . RouterBase::ACTION_INDEX;
    }

    protected function actionConfig(): array
    {
        return $this->actionConfig;
    }

    protected function popupLoad(string $alias): ElementPopup
    {
        return ElementPopup::builder($this->actionConfig()[$alias]);
    }

    public function grid(
        $dataProvider,
        $searchModel = null,
        string $title = '',
        array $clickClassMap = [],
        array $actionColumn = []
    ) {
        $grid = ElementGrid::builder($this->actionConfig()[$this->aliasGrid]);
        $grid->dataProviderSet($dataProvider);
        $grid->searchModelSet($searchModel);
        $grid->titleSet($title);
        $grid->clickClassMapSet($clickClassMap);
        $grid->actionColumnSet($actionColumn);

        return $grid;
    }

    public function viewer()
    {
        return $this->popupLoad($this->aliasViewer);
    }

    public function editor()
    {
        return $this->popupLoad($this->aliasEditor);
    }

    public function creator()
    {
        return $this->popupLoad($this->aliasCreator);
    }

    public function eraser()
    {
        return $this->popupLoad($this->aliasEraser);
    }

    public function duplicator()
    {
        return $this->popupLoad($this->aliasDuplicator);
    }

    public function templatePath(string $template): string
    {
        return $this->viewRoot . trim($template, '/');
    }

    protected function autocompleteGrid(Model $model, string $attribute): AutoCompleteSelectGrid
    {
        return AutoCompleteSelectGrid::builder([
            'model'     => $model,
            'attribute' => $attribute,
        ]);
    }

    protected function frontConfigCrudDefault(string $gridClass, array $add = [], array $unset = []): array
    {
        return $this->loadCache()->personal(
            ['action-front-config' => $this->absoluteId()],
            function () use ($gridClass, $add, $unset) {
                $gridId = $this->gridId();
                $config = [
                    static::ALIAS_GRID       => [
                        'class'       => $gridClass,
                        'gridId'      => $gridId,
                        'tag'         => static::gridTag(),
                        'route'       => $this->router->route(RouterBase::ACTION_INDEX),
                        'allowAction' => $this->router->isAllowed(RouterBase::ACTION_INDEX),
                    ],
                    static::ALIAS_EDITOR     => [
                        'tag'          => $this->router->tag(RouterBase::ACTION_EDIT),
                        'route'        => $this->router->route(RouterBase::ACTION_EDIT),
                        'allowAction'  => $this->router->isAllowed(RouterBase::ACTION_EDIT),
                        'actionDelete' => $this->router->isAllowed(RouterBase::ACTION_DELETE) ?
                            $this->router->route(RouterBase::ACTION_DELETE) : '',
                        'reloadGridId' => $gridId,
                    ],
                    static::ALIAS_CREATOR    => [
                        'tag'          => $this->router->tag(RouterBase::ACTION_CREATE),
                        'route'        => $this->router->route(RouterBase::ACTION_CREATE),
                        'allowAction'  => $this->router->isAllowed(RouterBase::ACTION_CREATE),
                        'reloadGridId' => $gridId,
                    ],
                    static::ALIAS_DUPLICATOR => [
                        'tag'          => $this->router->tag(RouterBase::ACTION_DUPLICATE),
                        'route'        => $this->router->route(RouterBase::ACTION_DUPLICATE),
                        'allowAction'  => $this->router->isAllowed(RouterBase::ACTION_DUPLICATE),
                        'reloadGridId' => $gridId,
                    ],
                    static::ALIAS_ERASER     => [
                        'tag'          => $this->router->tag(RouterBase::ACTION_DELETE),
                        'route'        => $this->router->route(RouterBase::ACTION_DELETE),
                        'allowAction'  => $this->router->isAllowed(RouterBase::ACTION_DELETE),
                        'reloadGridId' => $gridId,
                    ],
                    static::ALIAS_VIEWER     => [
                        'tag'          => $this->router->tag(RouterBase::ACTION_VIEW),
                        'route'        => $this->router->route(RouterBase::ACTION_VIEW),
                        'allowAction'  => $this->router->isAllowed(RouterBase::ACTION_VIEW),
                        'reloadGridId' => $gridId,
                    ],
                ];

                if ($unset) {
                    foreach ($unset as $element) {
                        unset($config[$element]);
                    }
                }

                if ($add) {
                    $config = array_merge($config, $add);
                }

                return $config;
            }
        );
    }
}
