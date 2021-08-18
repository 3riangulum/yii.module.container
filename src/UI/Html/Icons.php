<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use rmrevin\yii\fontawesome\component\Icon;
use rmrevin\yii\fontawesome\FAB;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

final class Icons
{
    public const COLOR_RED   = 'color-red';
    public const COLOR_GREEN = 'color-green';
    public const COLOR_BLACK = 'color-black';

    public const DFE = [
        'title'       => '',
        'class'       => [

            ' withe-tooltip',
        ],
        'aria-hidden' => true,
        'data'        => [
            'placement' => 'top',
            'toggle'    => 'tooltip',
            'html'      => 1,
        ],
    ];

    public const GENDER = [
        'f' => ['fa-venus', 'color-rose'],
        'm' => ['fa-mars', 'color-blue'],
        'a' => ['fa-venus-mars', 'color-indigo'],
    ];

    public static function iconAwesome(array $class, string $tooltip = null, string $size = FAB::SIZE_LARGE): Icon
    {
        $class[] = FAB::$cssPrefix;

        $options = ArrayHelper::merge(
            self::DFE,
            [
                'title' => null !== $tooltip ? self::createTitle($tooltip) : '',
                'class' => $class,
            ]
        );

        return FAB::icon($class[0] ?? '', $options)->size($size);
    }

    protected static function glyphIconTooltipSpanOption(array $config, string $content = ''): string
    {
        return Html::tag(
            'span',
            $content,
            ArrayHelper::merge(self::DFE, $config)
        );
    }

    protected static function createTitle(string $content = '', array $config = []): string
    {
        return Html::tag(
            'span',
            $content,
            ArrayHelper::merge(
                [
                    'class' => [
                        'strong',
                        'uppercase',
                    ],
                ],
                $config
            )
        );
    }

    protected static function spGreen(string $content): string
    {
        return Html::tag(
            'span',
            $content,
            ['class' => self::COLOR_GREEN]
        );
    }

    protected static function spRed(string $content): string
    {
        return Html::tag(
            'span',
            $content,
            ['class' => self::COLOR_RED]
        );
    }

    public static function isOnline(bool $online, string $class = '', array $data = []): string
    {
        switch ($online) {
            case true:
                $color = self::COLOR_GREEN;
                $icon = 'open';
                $title = 'ONLINE';
                break;
            case false:
                $color = self::COLOR_RED;
                $icon = 'close';
                $title = 'OFFLINE';
                break;
            default:
                $color = self::COLOR_BLACK;
                $icon = 'close';
                $title = 'UNDEFINED';
        }

        return self::glyphIconTooltipSpanOption([
            'title' => self::createTitle($title, ['class' => [$color]]),
            'class' => ["glyphicon-eye-$icon", $color, $class],
            'data'  => $data,
        ]);
    }

    public static function emailStatus($status): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => self::createTitle(
                    'Marketing Messages [email] : ' .
                    ($status ? self::spGreen('Subscribed') : self::spRed('UnSubscribed'))
                ),
                'class' => [
                    'glyphicon-envelope',
                    $status ? self::COLOR_GREEN : self::COLOR_RED,
                ],
            ]
        );
    }

    public static function iconTelegram(string $tooltip = null): string
    {
        return self::iconAwesome(['a fa-telegram-plane'], $tooltip);
    }

    public static function iconMail(string $tooltip = null): string
    {
        return self::iconAwesome(['a fa-envelope-o'], $tooltip);
    }

    public static function eur(string $title = ''): string
    {
        return $title . '<small><span class="small glyphicon glyphicon-euro" aria-hidden="true"></span></small>';
    }

    public static function info(string $class = ''): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => 'Info',
                'class' => [
                    'glyphicon-info-sign font',
                    $class,
                ],
            ]
        );
    }

    public static function refresh(string $class = '', string $title = 'Reload'): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-refresh font',
                    $class,
                ],
            ]
        );
    }

    public static function schedule(string $class = '', string $title = 'Schedule'): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-time font',
                    $class,
                ],
            ]
        );
    }

    public static function earphone(string $class = '', string $title = 'Call', array $data = []): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-earphone font',
                    $class,
                ],
                'data'  => $data,
            ]
        );
    }

    public static function okCircle(string $class = '', string $title = 'Ok', array $data = []): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-ok-circle font',
                    $class,
                ],
                'data'  => $data,
            ]
        );
    }

    public static function okCircleGreen(string $class = '', string $title = '', array $data = []): string
    {
        return static::okCircle(static::COLOR_GREEN . ' ' . $class, $title, $data);
    }

    public static function banCircle(string $class = '', string $title = 'Call', array $data = []): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-ban-circle font',
                    $class,
                ],
                'data'  => $data,
            ]
        );
    }

    public static function banCircleRed(string $class = '', string $title = '', array $data = []): string
    {
        return static::banCircle(static::COLOR_RED . ' ' . $class, $title, $data);
    }

    public static function plus(string $class = '', string $title = 'Add', array $data = []): string
    {
        return self::glyphIconTooltipSpanOption(
            [
                'title' => $title,
                'class' => [
                    'glyphicon glyphicon-plus font',
                    $class,
                ],
                'data'  => $data,
            ]
        );
    }
}
