<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

trait RepositoryI18nCollection
{
    public string $entityI18nClass   = '';
    public array $langMap           = [];
    public array $exportExcludeI18n = [];

    private ?array $i18nCollection = null;

    public function getLangMap(): array
    {
        return $this->langMap;
    }

    public function getLangList(): array
    {
        return array_keys($this->langMap);
    }

    public function entityI18nCreate(): DbModelBase
    {
        return new $this->entityI18nClass();
    }
}
