<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\db\ActiveRecord;

abstract class DbModelBase extends ActiveRecord
{
    public const DATE_FORMAT      = 'Y-m-d';
    public const TIME_FORMAT      = 'H:i:s';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function tbName(string $field = ''): string
    {
        $field = !empty($field) ? '.' . $field : '';

        return get_called_class()::getTableSchema()->fullName . $field;
    }

    public function dataSave(array $postData): bool
    {
        return $this->load($postData) && $this->save();
    }

    public function dataValidate(array $postData): bool
    {
        return $this->load($postData) && $this->validate();
    }
}
