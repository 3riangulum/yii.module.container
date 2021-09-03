<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use anticdroid\sortablegrid\SortableGridBehavior;

trait DbModelSortable
{
    public function behaviors()
    {
        $behavior = parent::behaviors();
        $behavior[$this->sortableAction] = [
            'class'             => SortableGridBehavior::class,
            'sortableAttribute' => $this->sortableAttribute,
        ];

        return $behavior;
    }
}
