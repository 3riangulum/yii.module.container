<?php

namespace Triangulum\Yii\ModuleContainer\UI\Data;

use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\data\Sort;

class ArrayDataProviderBase extends ArrayDataProvider
{
    public const ALIAS_PAGE_NUMBER     = 'page';
    public const ALIAS_PER_PAGE_NUMBER = 'per-page';
    public const ALIAS_SORT            = 'sort';

    public const DEF_PAGE_NUMBER     = 1;
    public const DEF_PER_PAGE_NUMBER = 50;

    public string $aliasSort            = self::ALIAS_SORT;
    public string $aliasPageNumber      = self::ALIAS_PAGE_NUMBER;
    public string $aliasPerPageNumber   = self::ALIAS_PER_PAGE_NUMBER;
    public int    $defaultPageNumber    = self::DEF_PAGE_NUMBER;
    public int    $defaultPerPageNumber = self::DEF_PER_PAGE_NUMBER;

    private ?string $route = null;

    public function setRoute(string $route = null): void
    {
        $this->route = $route;
    }

    private function routeGet(): ?string
    {
        return $this->route;
    }

    /**
     * @param array|bool|Sort $value
     */
    public function sortSet($value): void
    {
        if (is_array($value)) {
            $value['route'] = $this->routeGet();
        }

        parent::setSort($value);
    }

    public static function loadEmptyProvider(string $route = null): ArrayDataProvider
    {
        return new ArrayDataProvider([
            'allModels'  => [],
            'pagination' => [
                'pageSize' => self::DEF_PER_PAGE_NUMBER,
                'route'    => $route,
            ],
        ]);
    }

    protected function prepareTotalCount(): int
    {
        return $this->getPagination()->totalCount;
    }

    protected function prepareModels(): array
    {
        if (($models = $this->allModels) === null) {
            return [];
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
        }

        return $models;
    }

    /**
     * @param $currentPage
     * @param $perPage
     * @param $total
     * @throws InvalidArgumentException
     */
    public function initPagination($currentPage, $perPage, $total): void
    {
        $currentPage = (int)$currentPage;
        $perPage = (int)$perPage;
        $total = (int)$total;
        $currentPage = $currentPage > 0 ? $currentPage - 1 : $currentPage;
        $pagination = new Pagination(
            [
                'page'            => $currentPage,
                'defaultPageSize' => $perPage,
                'totalCount'      => $total,
            ]
        );

        $pagination->setPage($currentPage, true);
        $pagination->route = $this->routeGet();

        $this->setPagination($pagination);
    }

    public function loadPagination($currentPage, $perPage, $total): Pagination
    {
        $currentPage = (int)$currentPage;
        $perPage = (int)$perPage;
        $total = (int)$total;
        $currentPage = $currentPage > 0 ? $currentPage - 1 : $currentPage;
        $pagination = new Pagination(
            [
                'page'            => $currentPage,
                'defaultPageSize' => $perPage,
                'totalCount'      => $total,
            ]
        );

        $pagination->setPage($currentPage, true);
        $pagination->route = $this->routeGet();

        return $pagination;
    }

    public function getGridSortDirection(): string
    {
        $field = Yii::$app->getRequest()->getQueryParam($this->aliasSort);
        if (null === $field) {
            $sort = SORT_DESC;
        } elseif (0 !== strpos($field, '-')) {
            $sort = SORT_ASC;
        } else {
            $sort = SORT_DESC;
        }

        return SORT_ASC === $sort ? 'asc' : 'desc';
    }

    public function getGridSortField(string $default = null): ?string
    {
        $field = Yii::$app->getRequest()->getQueryParam($this->aliasSort);
        if (0 === strpos($field, '-')) {
            $field = substr($field, 1) . '';
        }

        return null === $field && null !== $default ? $default : $field;
    }

    public function requestPageNumberGet(): int
    {
        return (int)Yii::$app
            ->getRequest()
            ->getQueryParam(
                $this->aliasPageNumber,
                $this->defaultPageNumber
            );
    }

    public function requestPerPageNumberGet(): int
    {
        return (int)Yii::$app
            ->getRequest()
            ->getQueryParam(
                $this->aliasPerPageNumber,
                $this->defaultPerPageNumber
            );
    }
}
