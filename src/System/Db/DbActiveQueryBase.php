<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use yii\db\ActiveQuery;
use yii\db\Expression;

class DbActiveQueryBase extends ActiveQuery
{
    /**
     * @param string $field
     * @param mixed  $value
     */
    public function first(string $field, $value)
    {
        return $this->andWhereFieldEq($field, $value)
            ->limit(1)
            ->one();
    }

    /**
     * @param string           $fieldName
     * @param DbModelBase|null $model
     * {@inheritdoc}
     */
    public function andIsNotNull(string $fieldName, DbModelBase $model = null)
    {
        $name = $model ? $model::tbName($fieldName) : $fieldName;

        return $this->andWhere(['IS NOT', $name, null]);
    }

    /**
     * @param string           $fieldName
     * @param DbModelBase|null $model
     * {@inheritdoc}
     */
    public function andIsNull(string $fieldName, DbModelBase $model = null)
    {
        $name = $model ? $model::tbName($fieldName) : $fieldName;

        return $this->andWhere(['IS', $name, null]);
    }

    /**
     * @param string           $fieldName
     * @param DbModelBase|null $model
     * {@inheritdoc}
     */
    public function andNotEmpty(string $fieldName, DbModelBase $model = null)
    {
        $name = $model ? $model::tbName($fieldName) : $fieldName;

        return $this->andWhere(
            [
                'AND',
                ['IS NOT', $name, null],
                ['!=', $name, ''],
            ]
        );
    }

    /**
     * @param string           $fieldName
     * @param DbModelBase|null $model
     * {@inheritdoc}
     */
    public function andIsEmpty(string $fieldName, DbModelBase $model = null)
    {
        $name = $model ? $model::tbName($fieldName) : $fieldName;

        return $this->andWhere(
            [
                'OR',
                ['IS', $name, null],
                ['=', $name, ''],
            ]
        );
    }

    /**
     * @param string           $fieldName
     * @param DbModelBase|null $model
     * {@inheritdoc}
     */
    public function andIsNotEmpty(string $fieldName, DbModelBase $model = null)
    {
        $name = $model ? $model::tbName($fieldName) : $fieldName;

        return $this->andWhere(
            [
                'AND',
                ['IS NOT', $name, null],
                ['!=', $name, ''],
            ]
        );
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andWhereLikeRight(string $field, string $value)
    {
        if (!empty($value)) {
            $value = $this->prepareLikeStatement($value) . '%';
        }

        $param = ':v' . md5($field . $value);

        return $this->andWhere(['like', $field, new Expression($param, [$param => $value])]);
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterLikeRight(string $field, string $value = null)
    {
        if (empty($value)) {
            return $this;
        }

        $value = $this->prepareLikeStatement($value) . '%';
        $param = ':v' . md5($field . $value);

        return $this->andWhere(['like', $field, new Expression($param, [$param => $value])]);
    }

    /**
     * @param string      $field
     * @param string|null $value
     * {@inheritdoc}
     */
    public function orFilterLikeRight(string $field, string $value = null)
    {
        if (empty($value)) {
            return $this;
        }

        $value = $this->prepareLikeStatement($value) . '%';
        $param = ':v' . md5($field . $value);

        return $this->orWhere(['like', $field, new Expression($param, [$param => $value])]);
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterOrLikeRight(array $data)
    {
        $conditions = [];
        foreach ($data as $field => $value) {
            if (empty($value)) {
                return $this;
            }

            $value = $this->prepareLikeStatement($value);
            $param = ':v' . md5($field . $value);
            $conditions[] = ['like', $field, new Expression($param, [$param => $value . '%'])];
        }

        return $this->andWhere(array_merge(['OR'], $conditions));
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterOrLike(array $data)
    {
        $conditions = [];
        foreach ($data as $field => $value) {
            if (empty($value)) {
                return $this;
            }

            $value = $this->prepareLikeStatement($value);
            $param = ':v' . md5($field . $value);
            $conditions[] = ['like', $field, new Expression($param, [$param => '%' . $value . '%'])];
        }

        return $this->andWhere(array_merge(['OR'], $conditions));
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterOrLikeManyRight(string $field, array $values)
    {
        $conditions = [];
        foreach ($values as $value) {
            $value = $this->prepareLikeStatement($value);
            $param = ':v' . md5($field . $value);
            $conditions[] = ['like', $field, new Expression($param, [$param => $value . '%'])];
        }

        return $this->andWhere(array_merge(['OR'], $conditions));
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterOrLikeManyLeft(string $field, array $values)
    {
        $conditions = [];
        foreach ($values as $value) {
            $value = $this->prepareLikeStatement($value);
            $param = ':v' . md5($field . $value);
            $conditions[] = ['like', $field, new Expression($param, [$param => '%' . $value])];
        }

        return $this->andWhere(array_merge(['OR'], $conditions));
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andFilterAndNotLikeLeft(array $data)
    {
        $conditions = [];
        foreach ($data as $field => $value) {
            if (empty($value)) {
                return $this;
            }

            $value = $this->prepareLikeStatement($value);
            $param = ':v' . md5($field . $value);
            $conditions[] = ['not like', $field, new Expression($param, [$param => '%' . $value])];
        }

        return $this->andWhere(array_merge(['OR'], $conditions));
    }

    /**
     * @param string $field
     * @param string $value
     * {@inheritdoc}
     */
    public function andWhereLike(string $field, string $value)
    {
        return $this->andWhere([
            'like',
            $field,
            $this->prepareLikeStatement($value),
        ]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldEq(string $field, $value): self
    {
        return $this->andWhere(['=', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldIn(string $field, array $value)
    {
        return $this->andWhere(['IN', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldNotIn(string $field, array $value)
    {
        return $this->andWhere(['NOT IN', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andFilterWhereFieldIn(string $field, $value)
    {
        return $this->andFilterWhere(['IN', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldGtE(string $field, $value)
    {
        return $this->andWhere(['>=', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldLtE(string $field, $value)
    {
        return $this->andWhere(['<=', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldLt(string $field, $value)
    {
        return $this->andWhere(['<', $field, $value]);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * {@inheritdoc}
     */
    public function andWhereFieldGt(string $field, $value)
    {
        return $this->andWhere(['>', $field, $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function abortQuery()
    {
        return $this->where('0=1');
    }

    /**
     * @param string $value
     * @return string
     */
    private function prepareLikeStatement(string $value): string
    {
        return str_replace(['_', '%'], ['\_', '\%'], $value);
    }
}
