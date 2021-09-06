<?php

namespace Triangulum\Yii\ModuleContainer\System\Content;

use Yii;
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

    public static function cutUp($string, $fromStart = 5, $fromEnd = 5, $placeHolder = '<span class="smaller">...</span>')
    {
        $encoding = Yii::$app ? Yii::$app->charset : 'UTF-8';

        if (mb_strlen($string, $encoding) <= $fromStart + $fromEnd) {
            return $string;
        }

        $ret = mb_substr($string, 0, $fromStart, $encoding);
        $ret .= $placeHolder;
        $ret .= mb_substr($string, mb_strlen($string, $encoding) - $fromEnd, $fromEnd, $encoding);

        return $ret;
    }
}
