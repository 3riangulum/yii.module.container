<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\Panel;

use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;
use Yii;
use yii\helpers\BaseHtmlPurifier;

class PanelBase extends BaseObjectUI
{
    public string $panelClass = 'panel-default slim';
    public string $viewBegin  = '';
    public string $viewEnd    = '';

    public function begin(string $title = null, bool $closeButton = true): string
    {
        return $this->render(
            $this->viewBegin,
            [
                'title'       => BaseHtmlPurifier::process($title),
                'panelClass'  => $this->panelClass,
                'closeButton' => $closeButton,
            ]
        );
    }

    public function end(): string
    {
        return $this->render($this->viewEnd);
    }

    protected function render(string $view, array $params = []): string
    {
        return Yii::$app->getView()->render($view, $params);
    }
}
