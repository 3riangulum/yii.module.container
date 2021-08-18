<?php

namespace Triangulum\Yii\ModuleContainer\System;

interface ComponentBuilderInterface
{
    public static function builder(array $params): self;
}
