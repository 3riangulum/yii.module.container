<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

/**
 * @property string $repositoryAlias
 */
abstract class DbSearchSortable extends DbSearchBase
{
    protected array $sortCriteriaParams = [];
    protected array $sortDataProvider   = [];

    public function canSortItemsByParams(): bool
    {
        if (empty($this->sortCriteriaParams)) {
            return true;
        }

        return
            $this->paramListExistNotEmpty($this->sortCriteriaParams) &&
            count($this->paramExportNotEmpty()) === count($this->sortCriteriaParams);
    }

    protected function getSortDataProvider(): array
    {
        return $this->sortDataProvider;
    }
}
