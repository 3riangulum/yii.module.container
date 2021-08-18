<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\Panel;

use yii\helpers\BaseHtmlPurifier;
use yii\helpers\Html;

class PanelGrid extends PanelBase
{
    public string $panelClass = 'panel-default slim';
    public string $viewBegin  = '_grid_begin';
    public string $viewEnd    = '_grid_end';

    public function begin(string $title = null, $resetUrl = null): string
    {
        $resetLink = !$resetUrl ?
            null :
            Html::a(
                '<span class="glyphicon glyphicon-refresh "></span>',
                $resetUrl,
                [
                    'title'     => 'Reset grid filters',
                    'class'     => 'form-reset-grid text-center',
                    'container' => '.panel-heading',
                    'html'      => true,
                ]
            );

        return $this->render(
            $this->viewBegin,
            [
                'title'      => BaseHtmlPurifier::process($title),
                'panelClass' => $this->panelClass,
                'resetLink'  => $resetLink,
            ]
        );
    }
}
