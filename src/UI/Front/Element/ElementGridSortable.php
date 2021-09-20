<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use anticdroid\sortablegrid\SortableGridView;
use Triangulum\Yii\ModuleContainer\System\Db\DbSearchSortable;
use Webmozart\Assert\Assert;
use yii\web\View;

/**
 * @property DbSearchSortable searchModel
 * @property SortableGridView gridWidgetClass
 */
abstract class ElementGridSortable extends ElementGrid
{
    public ?string $sortableAction = null;

    private ?bool $canSort = null;

    public function init(): void
    {
        parent::init();
        Assert::notEmpty($this->sortableAction, static::class . '::sortableAction is empty');
    }

    public function widget(): string
    {
        $config = $this->configure(
            $this->dataProvider,
            $this->searchModel
        );

        if ($this->canSortItems()) {
            $config['sortableAction'] = $this->sortableAction;
            $this->gridWidgetClass = SortableGridView::class;
        }

        return $this->gridWidgetClass::widget($config);
    }

    public function renderByPjax(View $view, string $title = ''): void
    {
        $this->titleSet($title);
        $this->clickEventRegister($view);
        $this->pjaxBegin();
        $this->render();
        $this->registerSortable($view);
        $this->pjaxEnd();
    }

    protected function canSortItems(): bool
    {
        if (null === $this->canSort) {
            $this->canSort =
                $this->searchModel &&
                $this->searchModel->canSortItemsByParams() &&
                $this->dataProvider &&
                $this->dataProvider->getCount() > 1;
        }

        return $this->canSort;
    }

    protected function registerSortable(View $view): void
    {
        if (!$this->canSortItems()) {
            return;
        }

        $id = $this->pjaxId();
        $view->registerJs(<<<JS

$("#$id")
.off("sortableSuccess")
.on("sortableSuccess", ".grid-view",function (e, ui) {
    var flashClass = 'flash-green';
    var itemTr = $('[data-key="' + ui.item.data('key') + '"]');
    itemTr.addClass(flashClass);
    setTimeout(
        function () {
            itemTr.removeClass(flashClass);
        },
        1000
    );
});
JS
        );
    }
}
