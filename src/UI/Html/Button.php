<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use yii\helpers\Html;

final class Button
{
    public const CSS_BTN_SCCS = 'btn btn-success btn-xs ';
    public const CSS_BTN_DEF  = 'btn btn-default btn-xs ';
    public const CSS_BTN_INF  = 'btn btn-info btn-xs ';
    public const CSS_BTN_WRN  = 'btn btn-warning btn-xs ';
    public const CSS_BTN_DNGR = 'btn btn-danger btn-xs ';

    public static function config(): ButtonConfig
    {
        return new ButtonConfig;
    }

    public static function ajaxLink(ButtonConfig $config): string
    {
        return Html::a(
            $config->getTitle(),
            $config->getUrl(),
            $config->exportOptions()
        );
    }

    public static function submit(string $title = 'Save', string $class = self::CSS_BTN_SCCS): string
    {
        return '<div class="clearfix"></div>' .
            '<div class="form-group ">' .
            Html::submitButton($title, ['class' => 'btn btn-xs center-block ' . $class]) .
            '</div>';
    }

    public static function submitBottom(string $title = 'Save', string $class = self::CSS_BTN_SCCS): string
    {
        $btn = self::submit($title, $class);

        return <<<HTML
<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="margin-10px">
                $btn
            </div>
        </div>
    </div>
HTML;
    }

    public static function submitTop(string $title = 'Save', string $class = self::CSS_BTN_SCCS): string
    {
        $btn = self::submit($title, $class);

        return <<<HTML
<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="margin-bottom-20">
                $btn
            </div>
        </div>
    </div>
HTML;
    }
}
