<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

class EntityCrudService implements EntityCrud
{
    public function entityPk(): array
    {
        return $this->entity()->primaryKey ?
            ['id' => $this->entity()->pkGet()] :
            [];
    }
}
