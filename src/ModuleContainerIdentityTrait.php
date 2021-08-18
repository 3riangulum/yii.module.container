<?php

namespace Triangulum\Yii\ModuleContainer;

use Triangulum\Yii\ModuleContainer\System\Cache\RedisPrefixedCache;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\Menu\MenuItem;
use Yii;

trait ModuleContainerIdentityTrait
{
    public ?string $moduleId    = null;
    public ?string $containerId = null;
    public ?string $viewRoot    = null;

    protected function loadModuleComponent(string $componentAlias)
    {
        return Yii::$app
            ->getModule($this->getModuleId())
            ->get($this->componentId($componentAlias));
    }

    public function loadMenuItem(): MenuItem
    {
        return $this->loadModuleComponent(MenuItem::ID);
    }

    protected function loadRouter(): RouterBase
    {
        return $this->loadModuleComponent(RouterBase::ID);
    }

    protected function loadCache(): RedisPrefixedCache
    {
        return $this->loadModuleComponent(RedisPrefixedCache::ID);
    }

    protected function getModuleId(): string
    {
        return $this->moduleId;
    }

    protected function getContainerId(): string
    {
        return $this->containerId;
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
        return $this->getModuleId() . '/' . $this->getContainerId();
    }
}
