<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

interface Repository
{
    public function query(): DbActiveQueryBase;

    /**
     * @deprecated
     * @param int  $pk
     * @param bool $throw
     * @return $this
     */
//    public function entityLoad(int $pk, bool $throw = true): self;

    public function entityCreate(): self;

    public function entityDuplicate(int $pk): self;

    public function entity(): ?\yii\db\ActiveRecord;

    public function entityPk(): array;

    public function entityPersist(array $payload): bool;

    public function entityDelete(): bool;

    public function getPersistStatus(): bool;
}
