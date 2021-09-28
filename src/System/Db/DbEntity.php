<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\db\ActiveRecord;

class DbEntity extends ActiveRecord
{
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function exportAttributes(array $exclude = []): array
    {
        $data = $this->getAttributes();

        foreach ($exclude as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
    }
}
