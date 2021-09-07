<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use DomainException;
use Webmozart\Assert\Assert;
use yii\base\BaseObject;

abstract class RepositoryBase extends BaseObject implements Repository
{
    public const ID = 'Repository';
    /**
     * @var null|DbModelBase
     */
    public $entityClass = null;

    public array $exportExclude = [
        'id',
        'cdate',
        'udate',
        'active',
        'status',
    ];

    private ?DbModelBase $entity        = null;
    protected bool       $persistStatus = false;

    public function init(): void
    {
        parent::init();
        Assert::notEmpty($this->entityClass);
    }

    public function entityCreate(): self
    {
        $this->entity = new $this->entityClass;

        return $this;
    }

    public function entityDuplicate(int $pk): self
    {
        $this->entityCreate();
        $this->entity()->setAttributes(
            $this->filterAttributes(
                $this->findEntity($pk)->toArray()
            )
        );

        return $this;
    }

    protected function entityExport(): array
    {
        return $this->entity()->exportAttributes(
            $this->exportExclude
        );
    }

    public function entityLoad(int $pk, bool $throw = true): self
    {
        $this->entity = $this->findEntity($pk, $throw);

        return $this;
    }

    public function find(): DbActiveQueryBase
    {
        return $this->entityClass::find();
    }

    protected function findEntity(int $pk, bool $throw = true): ?DbModelBase
    {
        $entity = $this->find()
            ->where(['=', 'id', $pk])
            ->limit(1)
            ->one();

        if ($throw && empty($entity)) {
            throw new DomainException("Entity {$this->entityClass}[$pk] not exist");
        }

        return $entity;
    }

    public function entity(): ?DbModelBase
    {
        return $this->entity;
    }

    public function entityDelete(): bool
    {
        return $this->entity()->delete();
    }

    public function getPersistStatus(): bool
    {
        return $this->persistStatus;
    }

    protected function setPersistStatus(bool $status): void
    {
        $this->persistStatus = $status;
    }

    public function entityPk(): array
    {
        return $this->entity()->primaryKey ?
            ['id' => $this->entity()->pkGet()] :
            [];
    }
}
