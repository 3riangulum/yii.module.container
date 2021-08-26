<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front\Element;

use Triangulum\Yii\ModuleContainer\System\ComponentBuilderInterface;
use Triangulum\Yii\ModuleContainer\UI\Html\Growl;
use Yii;
use yii\helpers\Html;
use yii\widgets\Pjax;

final class ElementLink extends ElementBase implements ComponentBuilderInterface
{
    public string $title = 'Link';
    public string $class = 'btn btn-danger btn-xs text-center center-block';

    public static function builder(array $params): self
    {
        $params['class'] = self::class;

        return Yii::createObject($params);
    }

    public function link(string $notify = ''): void
    {
        $this->pjaxBegin(false);
        if ($notify) {
            echo Growl::growlOk($notify, '');
        }

        echo $this->linkElement();
        $this->pjaxEnd();
    }

    public function linkElement(): string
    {
        return Html::a(
            $this->title,
            [$this->route()],
            [
                'class' => $this->class,
                'data'  => [
                    'method' => 'post',
                    'pjax'   => true,
                ],
            ]
        );
    }

    public function pjaxBegin(): void
    {
        Pjax::begin([
            'id'            => $this->pjaxId(),
            'linkSelector'  => false,
            'clientOptions' => ['skipOuterContainers' => true],
        ]);
    }

    public function pjaxEnd(): void
    {
        Pjax::end();
    }

    protected function pjaxId(): string
    {
        if (empty($this->pjaxId)) {
            $this->pjaxId = $this->tag . '_lnk_pjax';
        }

        return $this->pjaxId;
    }
}
