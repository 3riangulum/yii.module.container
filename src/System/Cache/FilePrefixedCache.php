<?php

namespace Triangulum\Yii\ModuleContainer\System\Cache;

use Closure;
use Triangulum\Yii\ModuleContainer\ModuleContainerIdentityTrait;
use Yii;
use yii\caching\FileCache;

class FilePrefixedCache extends FileCache
{
    use ModuleContainerIdentityTrait;

    public const ID = 'Cache';

    public function init(): void
    {
        parent::init();
        $this->keyPrefix = Yii::$app->cache->keyPrefix . $this->absoluteId() . '_';
    }

    public function personal(array $key, Closure $callable, int $duration = null)
    {
        $key['k_uid'] = Yii::$app->access->getUid();

        return $this->getOrSet($key, $callable, $duration);
    }
}
