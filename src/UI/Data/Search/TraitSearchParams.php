<?php

namespace Triangulum\Yii\ModuleContainer\UI\Data\Search;

use Carbon\Carbon;
use Triangulum\Yii\ModuleContainer\System\Db\DbModelBase;

trait TraitSearchParams
{
    protected string $gridSearchFormAlias = '';
    protected array  $gridSearchParams    = [];

    protected function initParams(array $params, string $formAlias): void
    {
        $this->setGridSearchFormAlias($formAlias);
        $this->setParams($params);
    }

    protected function paramExist(string $alias): bool
    {
        return isset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    protected function paramNotEmpty(string $alias): bool
    {
        $val = $this->paramGetValue($alias);
        if (is_string($val)) {
            $val = trim($val);
        }

        return !empty($val);
    }

    protected function paramExistNotEmpty(string $alias): bool
    {
        return $this->paramExist($alias) && $this->paramNotEmpty($alias);
    }

    protected function paramListExistNotEmpty(array $aliasList): bool
    {
        foreach ($aliasList as $alias) {
            if (!$this->paramExist($alias) || !$this->paramNotEmpty($alias)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    protected function paramGetValue(string $alias, $default = null)
    {
        return $this->gridSearchParams[$this->gridSearchFormAlias][$alias] ?? $default;
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetIfNotExist(string $alias, $value): void
    {
        if (!$this->paramExist($alias)) {
            $this->paramSetValue($alias, $value);
        }
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetValue(string $alias, $value): void
    {
        $this->gridSearchParams[$this->gridSearchFormAlias][$alias] = $value;
    }

    protected function paramUnset(string $alias): void
    {
        unset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    public function paramsGetAll(): array
    {
        return [
            $this->gridSearchFormAlias => $this->gridSearchParams[$this->gridSearchFormAlias] ?? [],
        ];
    }

    protected function setParams(array $params): void
    {
        $this->gridSearchParams = array_merge($this->gridSearchParams, $params);
    }

    protected function setGridSearchFormAlias(string $alias): void
    {
        $this->gridSearchFormAlias = $alias;
    }

    protected function paramDateStartToDateTime(string $paramName): void
    {
        if ($this->paramExistNotEmpty($paramName)) {
            $this->paramSetValue(
                $paramName,
                Carbon::createFromFormat(DbModelBase::DATE_FORMAT, $this->paramGetValue($paramName))
                    ->startOfDay()
                    ->format(DbModelBase::DATE_TIME_FORMAT)
            );
        }
    }

    protected function paramDateEndToDateTime(string $paramName): void
    {
        if ($this->paramExistNotEmpty($paramName)) {
            $this->paramSetValue(
                $paramName,
                Carbon::createFromFormat(DbModelBase::DATE_FORMAT, $this->paramGetValue($paramName))
                    ->endOfDay()
                    ->format(DbModelBase::DATE_TIME_FORMAT)
            );
        }
    }

    public function paramExportNotEmpty(): array
    {
        $ret = [];
        foreach ($this->paramsGetAll()[$this->gridSearchFormAlias] ?? [] as $name => $value) {
            if ($this->paramExistNotEmpty($name)) {
                $ret[$name] = $value;
            }
        }

        return $ret;
    }
}
