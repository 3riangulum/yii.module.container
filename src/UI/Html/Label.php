<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

final class Label
{
    public static function labelOnOff($val): string
    {
        $map = [
            1 => '<span class="label label-success">On</span>',
            0 => '<span class="label label-danger">Off</span>',
        ];

        if (null !== $val && isset($map[$val])) {
            return $map[$val];
        }

        return '';
    }

    public static function labelYn($val): string
    {
        $map = [
            1 => '<span class="label label-success">y</span>',
            0 => '<span class="label label-danger">n</span>',
        ];

        if (null !== $val && isset($map[$val])) {
            return $map[$val];
        }

        return '';
    }

    public static function labelYnInverted($val): string
    {
        $map = [
            1 => '<span class="label label-danger">y</span>',
            0 => '<span class="label label-success">n</span>',

        ];

        if (null !== $val && isset($map[$val])) {
            return $map[$val];
        }

        return '';
    }

    public static function danger(string $txt, string $class = ''): string
    {
        return '<span class="label label-danger ' . $class . '">' . $txt . '</span>';
    }

    public static function warning(string $txt, string $class = ''): string
    {
        return '<span class="label label-warning ' . $class . '">' . $txt . '</span>';
    }

    public static function def(string $txt, string $class = ''): string
    {
        return '<span class="label label-default ' . $class . '">' . $txt . '</span>';
    }

    public static function info(string $txt, string $class = ''): string
    {
        return '<span class="label label-info ' . $class . '">' . $txt . '</span>';
    }

    public static function success(string $txt, string $class = ''): string
    {
        return '<span class="label label-success ' . $class . '">' . $txt . '</span>';
    }

    public static function empty(string $txt, string $class = ''): string
    {
        return '<span class="label label-empty ' . $class . '">' . $txt . '</span>';
    }

    public static function emptyLeft(string $txt, string $class = ''): string
    {
        return self::empty($txt, 'text-left ' . $class);
    }
}
