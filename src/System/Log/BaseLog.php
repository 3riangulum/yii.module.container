<?php

namespace Triangulum\Yii\ModuleContainer\System\Log;

use Throwable;
use Triangulum\Yii\ModuleContainer\ModuleContainerIdentityTrait;
use Yii;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\web\Application;

class BaseLog extends BaseObject
{
    use ModuleContainerIdentityTrait;

    public const ID = 'Log';

    private int $uid = 0;

    public function init(): void
    {
        parent::init();
        if (Yii::$app instanceof Application) {
            $this->uid = Yii::$app->access->getUid();
        }
    }

    public function info($data): void
    {
        $this->log($data, $this->absoluteId(), Logger::LEVEL_INFO);
    }

    public function dbg($data, string $label = ''): void
    {
        $this->log(
            !empty($label) ? [$label => $data] : $data,
            'dbg-' . $this->absoluteId(),
            Logger::LEVEL_ERROR
        );
    }

    public function err($data): void
    {
        $this->log($data, 'error-' . $this->absoluteId(), Logger::LEVEL_ERROR);
    }

    private function log($data, string $name = 'unknown', $level = 1): void
    {
        Yii::getLogger()->log(
            $data,
            $level,
            $name
        );
    }

    public function onThrowable(Throwable $t, array $data = []): void
    {
        $this->err([
            'cid'  => $this->absoluteId(),
            'uid'  => $this->uid,
            'code' => $t->getCode(),
            'msg'  => $t->getMessage(),
            't'    => $t->getTraceAsString(),
            'data' => $data,
        ]);
    }
}
