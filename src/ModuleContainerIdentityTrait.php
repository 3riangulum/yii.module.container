<?php

namespace Triangulum\Yii\ModuleContainer;

use Triangulum\Yii\ModuleContainer\System\Cache\RedisPrefixedCache;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\Menu\MenuItem;
use Yii;
use yii\helpers\BaseInflector;
use yii\helpers\Inflector;

trait ModuleContainerIdentityTrait
{
    public ?string $moduleId    = null;
    public ?string $containerId = null;
    public ?string $viewRoot    = null;

    protected function loadModuleComponent(string $alias)
    {
        return Yii::$app
            ->getModule($this->getModuleId())
            ->get($this->componentId($alias));
    }

    public function loadMenuItem(string $alias = ''): MenuItem
    {
        return $this->loadModuleComponent(MenuItem::ID . $alias);
    }

    protected function loadRouter(string $alias = ''): RouterBase
    {
        return $this->loadModuleComponent(RouterBase::ID . $alias);
    }

    protected function loadCache(string $alias = ''): RedisPrefixedCache
    {
        return $this->loadModuleComponent(RedisPrefixedCache::ID . $alias);
    }

    protected function getModuleId(): string
    {
        return $this->moduleId;
    }

    protected function getContainerId(): string
    {
        return lcfirst(Inflector::id2camel($this->containerId, '_'));
    }

    protected function componentId(string $componentAlias): string
    {
        return $this->getContainerId() . $componentAlias;
    }

    protected function absoluteId(): string
    {
        return $this->getModuleId() . '_' . $this->getContainerId();
    }

    protected function containerUri(): string
    {
        return $this->getModuleId() . '/' . BaseInflector::camel2id($this->containerId);
    }
}
