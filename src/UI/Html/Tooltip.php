<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use Triangulum\Yii\ModuleContainer\System\Content\Purifier;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;

final class Tooltip
{
    public const TOOLTIP_CLASS         = 'withe-tooltip';
    public const TOOLTIP_PLACEMENT_TOP = 'top';
    public const TOOLTIP_DATA_TOGGLE   = 'tooltip';
    public const TOOLTIP_CONFIG        = [
        'title'       => '',
        'class'       => [self::TOOLTIP_CLASS],
        'aria-hidden' => true,
        'data'        => [
            'placement' => self::TOOLTIP_PLACEMENT_TOP,
            'toggle'    => self::TOOLTIP_DATA_TOGGLE,
            'html'      => 1,
        ],
    ];

    public static function simple(string $content, string $tooltip): string
    {
        return self::spanTooltipTag(
            $content,
            [
                'title' => self::tooltipTitle($tooltip),
                'class' => [
                    'label label-default',
                    'normal-size',
                    'normal-text',
                ],
            ]
        );
    }

    public static function spanTooltipTag(string $content, array $config): string
    {
        return Html::tag(
            'span',
            $content,
            ArrayHelper::merge(self::TOOLTIP_CONFIG, $config)
        );
    }

    public static function tooltipTitle(string $content = '', array $config = []): string
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

    public static function truncatedTooltip(
        string $data = null,
        int $length = 18,
        string $suffix = null,
        $asHtml = false,
        bool $cleanUp = false
    ): string {
        if (empty($data)) {
            return '';
        }

        if (mb_strlen($data, 'utf-8' ?: Yii::$app->charset) > $length) {
            $suffix = null !== $suffix ? $suffix : '<span class="redd">*</span>';
            if ($cleanUp) {
                $data = Purifier::getContentClean($data, false);
            }

            return Html::tag(
                'span',
                StringHelper::truncate($data, $length, $suffix, 'utf-8', $asHtml),
                [
                    'title'          => $data,
                    'data-toggle'    => 'tooltip',
                    'data-placement' => 'top',
                    'class'          => 'orange-tooltip',
                    //                'html'           => 1,
                ]
            );
        }

        if ($cleanUp) {
            $data = Purifier::getContentClean($data, false);
        }

        return $data;
    }
}
