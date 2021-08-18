<?php

namespace Triangulum\Yii\ModuleContainer\UI\Data\Search;

use Yii;

trait TraitSearchSort
{
    private string $gridSortDefaultOrderIndex = 'defaultOrder';
    private array  $filterSortConfig          = [];

    public function gridSortSetSortConfig(array $filterSortConfig): void
    {
        $this->filterSortConfig = $filterSortConfig;
    }

    private function gridSortGetSortConfig(): array
    {
        return $this->filterSortConfig;
    }

    /**
     * @param null $default
     * @return mixed|string|null
     */
    private function gridSortGetParamName($default = null)
    {
        $field = Yii::$app->getRequest()->getQueryParam('sort');
        if (0 === strpos($field, '-')) {
            $field = substr($field, 1) . '';
        }

        return null === $field && null !== $default ? $default : $field;
    }

    private function gridSortGetDefaultData(): array
    {
        return $this->gridSortGetSortConfig()[$this->gridSortDefaultOrderIndex] ?? [];
    }

    private function gridSortGetParamNameDefault(): ?string
    {
        return trim(array_keys($this->gridSortGetDefaultData())[0] ?? null);
    }

    private function gridSortGetParamDirectionDefault(): string
    {
        return array_values($this->gridSortGetDefaultData())[0] ?? '';
    }

    private function gridSortHasParamSort(): bool
    {
        return !empty(Yii::$app->getRequest()->getQueryParam('sort'));
    }

    private function gridSortGetParamDirection(int $defaultSort = SORT_DESC): int
    {
        $field = Yii::$app->getRequest()->getQueryParam('sort');
        if (null === $field) {
            $sort = SORT_DESC;
        } elseif (0 !== strpos($field, '-')) {
            $sort = SORT_ASC;
        } else {
            $sort = SORT_DESC;
        }

        return $sort;
    }
}
