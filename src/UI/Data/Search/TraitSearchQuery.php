<?php

namespace Triangulum\Yii\ModuleContainer\UI\Data\Search;

use Triangulum\Yii\ModuleContainer\System\Db\DbActiveQueryBase;

trait TraitSearchQuery
{
    private ?DbActiveQueryBase $gridSearchQuery = null;

    public function getQuery(): DbActiveQueryBase
    {
        return $this->gridSearchQuery;
    }

    public function getQueryClone(): DbActiveQueryBase
    {
        return clone $this->gridSearchQuery;
    }

    public function setQuery(DbActiveQueryBase $query): void
    {
        $this->gridSearchQuery = clone $query;
    }
}
