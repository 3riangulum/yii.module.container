<?php

namespace Triangulum\Yii\ModuleContainer\System\Db\Services;

use Triangulum\Yii\ModuleContainer\System\Db\DbModelBase;

interface RepositoryContract
{
    public function entityLoad(int $pk, bool $throw = true): self;

    public function entityCreate(): self;

    public function entityDuplicate(int $pk): self;

    public function entity(): ?DbModelBase;

    public function entityPk(): array;

    public function entityPersist(array $payload): bool;

    public function entityDelete(): bool;

    public function getPersistStatus(): bool;
}
