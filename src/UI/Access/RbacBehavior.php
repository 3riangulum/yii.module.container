<?php

namespace Triangulum\Yii\ModuleContainer\UI\Access;


use Throwable;
use Webmozart\Assert\Assert;
use Yii;
use yii\base\ActionEvent;
use yii\base\InlineAction;
use yii\base\Module;
use yii\behaviors\AttributeBehavior;
use yii\di\Instance;
use yii\filters\AccessRule;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use yii\web\User;

class RbacBehavior extends AttributeBehavior
{
    public array $rules              = [];
    public array $protect            = [];
    public array $systemDefaultRoute = [];
    public array $languageList       = [];
    public string $login_url          = '';
    public ?string $redirect_url       = null;
    public ?string $roleRoot           = null;

    private array $rulesRbac = [];

    public function init(): void
    {
        parent::init();
        Assert::notEmpty($this->roleRoot);
    }

    public function events(): array
    {
        return [
            Module::EVENT_BEFORE_ACTION => 'interception',
        ];
    }

    private function getSystemDefaultRoute(): array
    {
        return $this->systemDefaultRoute;
    }

    private function routeResolve(): array
    {
        try {
            $route = Yii::$app->getRequest()->resolve();
        } catch (Throwable $t) {
            $route = $this->getSystemDefaultRoute();
        }

        $routeParts = explode(DS, $route[0] ?? '');
        if (in_array($routeParts[0] ?? '', $this->languageList, true)) {
            unset($routeParts[0]);
            $route[0] = empty($routeParts) ? $this->getSystemDefaultRoute()[0] : implode(DS, $routeParts);
        }

        return $route;
    }

    public function interception(ActionEvent $event): void
    {
        $route = $this->routeResolve();
        if (!empty($this->protect)) {
            $needToBeProtected = false;
            $routes = $this->createPartRoutes($route);
            foreach ($routes as $routeVariant) {
                if (in_array($routeVariant, $this->protect, true)) {
                    $needToBeProtected = true;
                    break;
                }
            }
            if (!$needToBeProtected) {
                return;
            }
        }

        //Check rules by config
        $this->createRule();
        /* @var $user User */
        $user = Instance::ensure(Yii::$app->user, User::class);
        $request = Yii::$app->getRequest();

        /** @var InlineAction $action */
        $action = $event->action;
        if (!$this->checkByRule($action, $user, $request)) {
            //Check rules by AuthManager
            if (!$this->checkPermission($route)) {
                if (Yii::$app->user->isGuest && $this->login_url) {
                    Yii::$app->response->redirect($this->login_url)->send();
                    exit();
                }
                if ($this->redirect_url) {
                    Yii::$app->response->redirect($this->redirect_url)->send();
                    exit();
                }

                $message = 'You not allow to access! ';
                if (Yii::$app->user->can($this->roleRoot)) {
                    $message .= 'Route:' . ($route[0] ?? '');
                }

                throw new ForbiddenHttpException($message);
            }
        }
    }

    protected function createRule(): void
    {
        foreach ($this->rules as $controller => $rule) {
            foreach ($rule as $singleRule) {
                if (is_array($singleRule)) {
                    $option = [
                        'controllers' => [$controller],
                        'class'       => AccessRule::class,
                    ];
                    $this->rulesRbac[$controller] = Yii::createObject(array_merge($option, $singleRule));
                }
            }
        }
    }

    protected function checkByRule(InlineAction $action, User $user, Request $request): bool
    {
        /** @var AccessRule $rule */
        foreach ($this->rulesRbac as $rule) {
            if ($rule->allows($action, $user, $request)) {
                return true;
            }
        }

        return false;
    }

    protected function checkPermission(array $route): bool
    {
        try {
            //$route[0] - is the route, $route[1] - is the associated parameters
            $routes = $this->createPartRoutes($route);
            foreach ($routes as $routeVariant) {
                if (Yii::$app->access->can($routeVariant)) {
                    return true;
                }
            }
        } catch (Throwable $t) {
            return false;
        }

        return false;
    }

    protected function createPartRoutes(array $route): array
    {
        //$route[0] - is the route, $route[1] - is the associated parameters
        $routePathTmp = explode('/', $route[0]);
        $result = [];
        $routeVariant = array_shift($routePathTmp);
        $result[] = $routeVariant;

        foreach ($routePathTmp as $routePart) {
            $routeVariant .= '/' . $routePart;
            $result[] = $routeVariant;
        }

        return $result;
    }
}
