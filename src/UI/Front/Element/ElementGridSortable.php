<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use anticdroid\sortablegrid\SortableGridView;
use yii\web\View;

trait ElementGridSortable
{
    public string $sortableAction = 'sortableAction';

    public function init(): void
    {
        parent::init();
        $this->gridWidgetClass = SortableGridView::class;
    }

    public function widget(): string
    {
        $config = $this->configure(
            $this->dataProvider,
            $this->searchModel
        );
        $config['sortableAction'] = $this->sortableAction;

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

    protected function registerSortable(View $view): void
    {
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
