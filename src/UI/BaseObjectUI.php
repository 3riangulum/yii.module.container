<?php

namespace Triangulum\Yii\ModuleContainer\UI;

use Webmozart\Assert\Assert;
use Yii;
use yii\base\BaseObject;
use yii\web\Application;

class BaseObjectUI extends BaseObject
{
    public function init(): void
    {
        Assert::isInstanceOf(Yii::$app, Application::class);
        parent::init();
    }
}
