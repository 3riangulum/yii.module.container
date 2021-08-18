<?php

namespace Triangulum\Yii\ModuleContainer;

use ReflectionClass;
use Webmozart\Assert\Assert;
use Yii;
use yii\base\Module;
use yii\console\Application;

class ModuleBase extends Module
{
    public string  $moduleParentNamespace  = '';
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
        if ($this->moduleBootstrapUI) {
            Yii::configure($this, $this->getBootstrapConfig($this->moduleBootstrapUI));
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
        $reflector = new ReflectionClass(get_class($this));
        $config = require dirname($reflector->getFileName()) . $path;

        $componentsConfig = [];
        foreach ($config as $containerId => $components) {
            foreach ($components as $alias => $component) {
                $component['moduleId'] = $this->id;
                $component['containerId'] = $containerId;
                $component['viewRoot'] = $this->viewRoot($containerId);

                $componentAlias = $containerId === $alias ? $component['class'] : $containerId . $alias;
                $componentsConfig['components'][$componentAlias] = $component;
            }
        }

        return $componentsConfig;
    }

    protected function viewRoot(string $containerId): string
    {
        return $this->viewPath . '/' . $containerId . '/';
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