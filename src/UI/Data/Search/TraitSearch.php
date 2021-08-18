<?php

namespace Triangulum\Yii\ModuleContainer\UI\Data\Search;

use Triangulum\Yii\ModuleContainer\UI\Data\ArrayDataProviderBase;
use yii\data\ArrayDataProvider;

trait TraitSearch
{
    use TraitSearchQuery;
    use TraitSearchParams;
    use TraitSearchSort;
    use TraitSearchFilter;

    public function gridProviderGetEmpty(string $route = null): ArrayDataProvider
    {
        return ArrayDataProviderBase::loadEmptyProvider($route);
    }
}
