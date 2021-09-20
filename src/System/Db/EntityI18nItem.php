<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

trait EntityI18nItem
{
    public function formName(): string
    {
        return parent::formName() . '[' . $this->getLang() . ']';
    }

    public function formNameOriginal(): string
    {
        return parent::formName();
    }

    public function getLang(): string
    {
        return (string)$this->{$this->languageKey};
    }

    public function setLang(string $lang): void
    {
        $this->{$this->languageKey} = $lang;
    }

    public function getParentPid(): int
    {
        return (int)$this->{$this->parentKey};
    }

    public function setParentPid(int $tariffId): void
    {
        $this->{$this->parentKey} = $tariffId;
    }
}
