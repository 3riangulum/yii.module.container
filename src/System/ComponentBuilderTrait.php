<?php

namespace Triangulum\Yii\ModuleContainer\System;

use Yii;

trait ComponentBuilderTrait
{
    public static function builder(array $params): self
    {
        if (!isset($params['class'])) {
            $params['class'] = static::class;
        }

        return Yii::createObject($params);
    }
}
