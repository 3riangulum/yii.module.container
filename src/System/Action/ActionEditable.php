<?php

namespace Triangulum\Yii\ModuleContainer\System\Action;

use Closure;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

/**
 * Fork yii2mod\editable\EditableAction
 */
class ActionEditable extends Action
{
    public ?string $modelClass = null;

    public string $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @var null|Closure a function to be called previous saving model. The anonymous function is preferable to have the
     * model passed by reference. This is useful when we need to set model with extra data previous update
     */
    public ?Closure $preProcess = null;

    /**
     * @var null|Closure a function to be called after success saving model. The anonymous function is preferable
     * to have the model passed by reference. This is useful when we need to set model with extra data previous update
     */
    public ?Closure $postModelPush = null;

    /**
     * @var bool whether to create a model if a primary key parameter was not found
     */
    public bool $forceCreate = false;

    /**
     * @var string default pk column name
     */
    public string $pkColumn = 'id';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
    }

    /**
     * Runs the action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function run(): bool
    {
        $model = $this->findModelOrCreate();
        $attribute = $this->getModelAttribute();

        if ($this->preProcess && is_callable($this->preProcess, true)) {
            call_user_func($this->preProcess, $model);
        }

        $model->setScenario($this->scenario);
        $model->$attribute = Yii::$app->request->post('value');

        if (!$model->validate([$attribute])) {
            throw new BadRequestHttpException($model->getFirstError($attribute));
        }

        if ($model->save(false)) {
            if ($this->postModelPush && is_callable($this->postModelPush, true)) {
                call_user_func($this->postModelPush, $model);
            }

            return true;
        }

        return false;
    }

    /**
     * @return array|mixed|string
     * @throws BadRequestHttpException
     */
    private function getModelAttribute()
    {
        $attribute = Yii::$app->request->post('name');

        if (strpos($attribute, '.')) {
            $attributeParts = explode('.', $attribute);
            $attribute = array_pop($attributeParts);
        }

        if ($attribute === null) {
            throw new BadRequestHttpException('Attribute cannot be empty.');
        }

        return $attribute;
    }

    /**
     * @return ActiveRecord
     * @throws BadRequestHttpException
     */
    private function findModelOrCreate(): ActiveRecord
    {
        $pk = unserialize(base64_decode(Yii::$app->request->post('pk')));
        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $model = $class::findOne(is_array($pk) ? $pk : [$this->pkColumn => $pk]);

        if (!$model) {
            if ($this->forceCreate) {
                $model = new $class();
            } else {
                throw new BadRequestHttpException('Entity not found by primary key ' . $pk);
            }
        }

        return $model;
    }
}
