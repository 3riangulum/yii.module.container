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

    public ?string $gridClass = null;

    protected ?RouterBase $router       = null;
    protected array $actionConfig = [];

    public function init(): void
    {
        parent::init();
        $this->router = $this->loadRouter();
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
        $grid = ElementGrid::builder($this->actionConfig()[self::ALIAS_GRID]);
        $grid->dataProviderSet($dataProvider);
        $grid->searchModelSet($searchModel);
        $grid->titleSet($title);
        $grid->clickClassMapSet($clickClassMap);
        $grid->actionColumnSet($actionColumn);

        return $grid;
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
}
