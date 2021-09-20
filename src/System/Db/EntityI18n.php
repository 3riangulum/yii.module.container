<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

interface EntityI18n
{
    public function getLang(): string;

    public function setLang(string $lang): void;

    public function getParentPid(): int;

    public function setParentPid(int $pid): void;

    public function formNameOriginal(): string;

    public function formName(): string;
}
