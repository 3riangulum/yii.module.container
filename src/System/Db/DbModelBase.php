<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\db\ActiveRecord;
use yii\db\Transaction;
use yii\redis\ActiveQuery;

abstract class DbModelBase extends ActiveRecord
{
    public const DATE_FORMAT      = 'Y-m-d';
    public const TIME_FORMAT      = 'H:i:s';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function dataSave(array $postData): bool
    {
        return $this->load($postData) && $this->save();
    }

    public function dataValidate(array $postData): bool
    {
        return $this->load($postData) && $this->validate();
    }

    public function getErrorsAsString(string $separator = '<br>'): string
    {
        if (!$this->errors) {
            return '';
        }

        $list = [];
        foreach ($this->errors as $name => $error) {
            $list[] = implode($separator, $error);
        }

        return implode($separator, $list);
    }

    public function exportAttributes(array $exclude = []): array
    {
        $data = $this->getAttributes();

        foreach ($exclude as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
    }

    public static function startTransaction(string $isolationLevel = Transaction::SERIALIZABLE): Transaction
    {
        return static::getDb()->beginTransaction($isolationLevel);
    }
}
