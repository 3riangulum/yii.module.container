<?php

namespace Triangulum\Yii\ModuleContainer\System\Model;

use ReflectionClass;
use yii\base\Model;
use yii\helpers\Html;

class ModelBase extends Model
{
    public function dataValidate(array $postData, bool $isValid = null): bool
    {
        if ($isValid !== null && !$isValid) {
            return false;
        }

        return $this->load($postData) && $this->validate();
    }

    public function formListErrorsAsString($separator = "<br>"): string
    {
        $propertyList = $this->attributeLabels();
        if (!$this->errors) {
            return '';
        }

        $ret = [];
        foreach ($this->errors as $alias => $errorList) {
            $name = !empty($propertyList[$alias]) ? $propertyList[$alias] : '';
            foreach ($errorList as $error) {
                $ret[] = $name . $error . $separator;
            }
        }

        return implode('', $ret);
    }

    public static function extractPost(array $postData, string $attribute = null)
    {
        $data = $postData[static::getFormName()] ?? [];

        if (empty($attribute)) {
            return $data;
        }

        return $data[$attribute] ?? null;
    }

    public static function getFormName(string $index = null): string
    {
        $reflector = new ReflectionClass(get_called_class());
        $suffix = $index ? '[' . $index . ']' : '';

        return $reflector->getShortName() . $suffix;
    }

    protected function exportNotEmpty(): array
    {
        $ret = [];
        $data = $this->toArray();
        foreach ($data as $field => $value) {
            if (empty($value)) {
                continue;
            }

            $ret[$field] = $value;
        }

        return $ret;
    }

    protected function htmlInputId(string $attribute): string
    {
        return Html::getInputId($this, $attribute);
    }
}
