<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

class LabelInline
{
    private const INLINE_CLASS = 'label-inline';

    public static function danger(string $txt, string $class = ''): string
    {
        return Label::danger($txt, self::INLINE_CLASS . ' ' . $class);
    }

    public static function warning(string $txt, string $class = ''): string
    {
        return Label::warning($txt, self::INLINE_CLASS . ' ' . $class);
    }

    public static function def(string $txt, string $class = ''): string
    {
        return Label::def($txt, self::INLINE_CLASS . ' ' . $class);
    }

    public static function info(string $txt, string $class = ''): string
    {
        return Label::info($txt, self::INLINE_CLASS . ' ' . $class);
    }

    public static function success(string $txt, string $class = ''): string
    {
        return Label::success($txt, self::INLINE_CLASS . ' ' . $class);
    }

    public static function invisible(string $txt, string $class = ''): string
    {
        $inline = self::INLINE_CLASS;

        return <<<HTML
<span class="label label-invisible $inline $class">$txt</span>
HTML
            ;
    }
}
