<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

interface RepositoryI18n
{
    public function loadI18nList(): array;

    public function getLangMap(): array;

    public function getLangList(): array;

    public function entityI18nCreate(): DbModelBase;
}
