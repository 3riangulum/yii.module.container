<?php

namespace Triangulum\Yii\ModuleContainer;

use ReflectionClass;
use Webmozart\Assert\Assert;
use Yii;
use yii\base\Module;
use yii\console\Application;
use yii\helpers\Inflector;

class ModuleBase extends Module
{
    public const COMPONENTS = 'components';

    public string $moduleParentNamespace  = '';
    public ?string $moduleBootstrapUI      = '/config/module_bootstrap_ui.php';
    public ?string $moduleBootstrapCommand = '/config/module_bootstrap_command.php';

    public function init(): void
    {
        $this->defineControllerNamespace();
        parent::init();

        if ($this->isCommandMode()) {
            $this->loadComponentsCommand();
        } else {
            $this->loadComponentsUI();
        }
    }

    protected function loadComponentsUI(): void
    {
        $config = $this->getBootstrapConfig($this->moduleBootstrapUI);
        if ($this->moduleBootstrapUI) {
            Yii::configure($this, $config);
        }
    }

    protected function loadComponentsCommand(): void
    {
        if ($this->moduleBootstrapCommand) {
            Yii::configure($this, $this->getBootstrapConfig($this->moduleBootstrapCommand));
        }
    }

    private function getBootstrapConfig(string $path): array
    {
        return Yii::$app->cache->getOrSet(
            [static::ID, $path],
            function () use ($path) {
                $reflector = new ReflectionClass(get_class($this));
                $config = require dirname($reflector->getFileName()) . $path;

                $componentsConfig = [];
                foreach ($config as $containerId => $components) {
                    foreach ($components as $alias => $component) {
                        if (self::COMPONENTS === $alias) {
                            foreach ($component as $index => $data) {
                                $componentAlias = lcfirst(Inflector::id2camel($containerId . $index, '_'));
                                $componentsConfig[self::COMPONENTS][$componentAlias] = $data;
                            }
                            continue;
                        }

                        $component['moduleId'] = $this->id;
                        $component['containerId'] = $containerId;
                        $component['viewRoot'] = $this->viewRoot($containerId);

                        $componentAlias = $containerId === $alias ?
                            $component['class'] :
                            lcfirst(Inflector::id2camel($containerId . $alias, '_'));

                        $componentsConfig[self::COMPONENTS][$componentAlias] = $component;
                    }
                }

                return $componentsConfig;
            }
        );
    }

    protected function viewRoot(string $containerId): string
    {
        return $this->viewPath . static::ID . '/views/' . $containerId . '/';
    }

    private function defineControllerNamespace(): void
    {
        Assert::notEmpty($this->moduleParentNamespace);

        $this->controllerNamespace =
            $this->moduleParentNamespace .
            '\\' . $this->id . '\\' .
            ($this->isCommandMode() ? 'commands' : 'controller');
    }

    private function isCommandMode(): bool
    {
        return Yii::$app instanceof Application;
    }
}
