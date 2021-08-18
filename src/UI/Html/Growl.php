<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use kartik\growl\Growl as KartikGrowl;

final class Growl
{
    public static function growl(
        string $title,
        string $message,
        bool $success = true,
        $delay = 0,
        $align = 'right'
    ): string {
        if (!$success && !$delay) {
            $delay = 10000;
        } elseif ($success && !$delay) {
            $delay = 2000;
        }

        return KartikGrowl::widget([
            'type'          => $success ? KartikGrowl::TYPE_SUCCESS : KartikGrowl::TYPE_DANGER,
            'icon'          => $success ? 'glyphicon glyphicon-ok-sign' : 'glyphicon glyphicon-warning-sign',
            'title'         => '  &nbsp; ' . $title,
            'showSeparator' => true,
            'body'          => $message,
            'delay'         => false,
            'pluginOptions' => [
                'z_index'         => 1052,
                'timer'           => 500,
                'delay'           => $delay,
                'showProgressbar' => true,
                'offset'          => 50,
                'placement'       => [
                    'from'  => 'top',
                    'align' => $align,
                    //                    'align' => 'right',
                    //                    'align' => 'center',
                ],
            ],
        ]);
    }

    public static function growlError(string $title, string $message, int $delay = 10000, $align = 'right'): string
    {
        return self::growl($title, $message, false, $delay, $align);
    }

    public static function growlOk(string $title, string $message, int $delay = 2000, $align = 'right'): string
    {
        return self::growl($title, $message, true, $delay, $align);
    }
}
