<?php

namespace Triangulum\Yii\ModuleContainer\UI;

use Throwable;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ControllerBase extends Controller
{
    protected int $hideModal             = 0;
    protected string $uri                   = '';
    protected array $csrfValidationExclude = [];
    protected array $jsonResponse          = [];
    protected array $accessRules           = [];
    protected array $verbActions           = [];

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (isset($this->jsonResponse[$action->id])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        if (isset($this->csrfValidationExclude[$action->id])) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        if (!$this->accessRules) {
            $this->accessRules = [$this->accessRuleDefault()];
        }

        if (!$this->verbActions) {
            $this->verbActions = $this->verbActionsCrudDefault();
        }

        $this->uri = $action->controller->id . '-' . $action->id;

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => $this->accessRules,
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => $this->verbActions,
            ],
        ];
    }

    protected function accessRuleDefault(array $actionList = []): array
    {
        if (empty($ipWhite = Yii::$app->params['App.IpWhiteList'] ?? [])) {
            $default =  [
                'allow' => true,
                'roles' => ['@'],
            ];
        } else {
            $default = [
                'allow' => true,
                'ips'   => $ipWhite,
            ];
        }

        if ($actionList) {
            $default['actions'] = $actionList;
        }

        return $default;
    }

    protected function verbActionsCrudDefault(): array
    {
        return [
            RouterBase::ACTION_DELETE => ['POST'],
        ];
    }

    protected function getUri(): string
    {
        return $this->uri;
    }

    protected function renderThrowable(Throwable $t): string
    {
        $viewPath = Yii::$app->params['App.UI.sys.template_throwable'];

        $data = [
            'error' => YII_DEBUG ? $t->getMessage() : 'Internal error',
            'trace' => YII_DEBUG ? $t->getTraceAsString() : '',
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($viewPath, $data);
        }

        return $this->render($viewPath, $data);
    }

    protected function renderViewAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_view'],
            $data
        );
    }

    protected function renderNotifyAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_notify'],
            $data
        );
    }

    protected function renderDeleteAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_delete'],
            $data
        );
    }

    protected function createTitle(string $title = ''): string
    {
        return 'Creation.' . $title;
    }

    protected function editTitle(string $title = ''): string
    {
        return 'Redaction.' . $title;
    }

    protected function duplicateTitle(string $title = ''): string
    {
        return 'Duplication.' . $title;
    }

    protected function deleteTitle(string $title = ''): string
    {
        return 'Deletion.' . $title;
    }

    /**
     * @param string $msg
     * @throws NotFoundHttpException
     */
    protected function notFoundHttpException(string $msg = 'The requested resource does not exist.'): void
    {
        throw new NotFoundHttpException($msg);
    }
}
