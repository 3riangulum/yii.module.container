<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\db\ActiveRecord;

class DbEntity extends ActiveRecord
{
    protected bool $dbSingleTransaction = true;

    public function transactions(): array
    {
        if ($this->dbSingleTransaction) {
            return [
                self::SCENARIO_DEFAULT => self::OP_ALL,
            ];
        }

        return [];
    }

    public function exportAttributes(array $exclude = []): array
    {
        $data = $this->getAttributes();

        foreach ($exclude as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
    }

    public function disableDbSingleTransaction(): void
    {
        $this->dbSingleTransaction = false;
    }

    public static function tbName(string $field = ''): string
    {
        $field = !empty($field) ? '.' . $field : '';

        return get_called_class()::getTableSchema()->fullName . $field;
    }
}
