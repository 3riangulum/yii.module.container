<?php

namespace Triangulum\Yii\ModuleContainer\System\Content;

use yii\helpers\HtmlPurifier;

final class Purifier
{
    public static function getContentClean(string $content = null, bool $removeLine = false): string
    {
        if (empty($content)) {
            return '';
        }

        $content = trim(
            strip_tags(
                HtmlPurifier::process($content)
            )
        );

        if ($removeLine) {
            $content = str_replace(["\\r\\n", "\\r", "\\n", "\\t"], '', $content);
            $content = trim(preg_replace('/\s\s+/', ' ', $content));
        }

        return $content;
    }

    public static function removeQuotes(string $text): string
    {
        return str_replace(['"', "'"], "", $text);
    }
}
