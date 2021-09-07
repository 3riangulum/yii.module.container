<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\data\DataProviderInterface;

interface DbSearch
{
    public function search(array $params): DataProviderInterface;
}
