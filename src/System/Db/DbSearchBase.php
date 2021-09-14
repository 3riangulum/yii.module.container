<?php

namespace Triangulum\Yii\ModuleContainer\System\Db;

use Carbon\Carbon;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use ReflectionClass;
use Triangulum\Yii\ModuleContainer\System\Model\ModelBase;
use Triangulum\Yii\ModuleContainer\UI\Data\ArrayDataProviderBase;
use Triangulum\Yii\ModuleContainer\UI\Html\CheckBox;
use Triangulum\Yii\ModuleContainer\UI\Html\CheckboxWidget;
use Triangulum\Yii\ModuleContainer\UI\Html\Dropdown\Dropdown;
use Triangulum\Yii\ModuleContainer\UI\Html\Dropdown\FilterDropdown;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

abstract class DbSearchBase extends ModelBase implements DbSearch
{
    public const ID = 'Search';

    public string $repositoryAlias = '';
    public ?Repository $repository      = null;

    private ?DbActiveQueryBase $gridSearchQuery           = null;
    protected string $gridSearchFormAlias       = '';
    protected array $gridSearchParams          = [];
    private string $gridSortDefaultOrderIndex = 'defaultOrder';
    private array $filterSortConfig          = [];
    private static ?string $filterPrefix              = null;
    private ?array $gridSearchFilterVal       = null;
    private string $elementClass              = 'form-control';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->repository = Yii::$app->get($this->repositoryAlias);
    }

    public function gridProviderGetEmpty(string $route = null): ArrayDataProvider
    {
        return ArrayDataProviderBase::loadEmptyProvider($route);
    }

    public function getQuery(): DbActiveQueryBase
    {
        return $this->gridSearchQuery;
    }

    public function getQueryClone(): DbActiveQueryBase
    {
        return clone $this->gridSearchQuery;
    }

    public function setQuery(DbActiveQueryBase $query): void
    {
        $this->gridSearchQuery = clone $query;
    }

    protected function initParams(array $params, string $formAlias): void
    {
        $this->setGridSearchFormAlias($formAlias);
        $this->setParams($params);
    }

    protected function paramExist(string $alias): bool
    {
        return isset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    protected function paramNotEmpty(string $alias): bool
    {
        $val = $this->paramGetValue($alias);
        if (is_string($val)) {
            $val = trim($val);
        }

        return !empty($val);
    }

    protected function paramExistNotEmpty(string $alias): bool
    {
        return $this->paramExist($alias) && $this->paramNotEmpty($alias);
    }

    protected function paramListExistNotEmpty(array $aliasList): bool
    {
        foreach ($aliasList as $alias) {
            if (!$this->paramExist($alias) || !$this->paramNotEmpty($alias)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    protected function paramGetValue(string $alias, $default = null)
    {
        return $this->gridSearchParams[$this->gridSearchFormAlias][$alias] ?? $default;
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetIfNotExist(string $alias, $value): void
    {
        if (!$this->paramExist($alias)) {
            $this->paramSetValue($alias, $value);
        }
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetValue(string $alias, $value): void
    {
        $this->gridSearchParams[$this->gridSearchFormAlias][$alias] = $value;
    }

    protected function paramUnset(string $alias): void
    {
        unset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    public function paramsGetAll(): array
    {
        return [
            $this->gridSearchFormAlias => $this->gridSearchParams[$this->gridSearchFormAlias] ?? [],
        ];
    }

    protected function setParams(array $params): void
    {
        $this->gridSearchParams = array_merge($this->gridSearchParams, $params);
    }

    protected function setGridSearchFormAlias(string $alias): void
    {
        $this->gridSearchFormAlias = $alias;
    }

    protected function paramDateStartToDateTime(string $paramName): void
    {
        if ($this->paramExistNotEmpty($paramName)) {
            $this->paramSetValue(
                $paramName,
                Carbon::createFromFormat(DbModelBase::DATE_FORMAT, $this->paramGetValue($paramName))
                    ->startOfDay()
                    ->format(DbModelBase::DATE_TIME_FORMAT)
            );
        }
    }

    protected function paramDateEndToDateTime(string $paramName): void
    {
        if ($this->paramExistNotEmpty($paramName)) {
            $this->paramSetValue(
                $paramName,
                Carbon::createFromFormat(DbModelBase::DATE_FORMAT, $this->paramGetValue($paramName))
                    ->endOfDay()
                    ->format(DbModelBase::DATE_TIME_FORMAT)
            );
        }
    }

    public function paramExportNotEmpty(): array
    {
        $ret = [];
        foreach ($this->paramsGetAll()[$this->gridSearchFormAlias] ?? [] as $name => $value) {
            if ($this->paramExistNotEmpty($name)) {
                $ret[$name] = $value;
            }
        }

        return $ret;
    }

    private static function filterPrefix(): string
    {
        if (null === static::$filterPrefix) {
            $reflector = new ReflectionClass(get_called_class());

            static::$filterPrefix = $reflector->getShortName();
        }

        return static::$filterPrefix;
    }

    private function getFilterValues(): array
    {
        if (null === $this->gridSearchFilterVal) {
            $this->gridSearchFilterVal = Yii::$app
                ->getRequest()
                ->getQueryParam(
                    $this->getFormAlias(),
                    []
                );
        }

        return $this->gridSearchFilterVal;
    }

    private function getFormAlias(): string
    {
        return $this->formName();
    }

    private function getFilterInputName(string $field): string
    {
        return $this->getFormAlias() . '[' . $field . ']';
    }

    /**
     * @return mixed
     */
    private function getFilterValue(string $alias, $default = null)
    {
        $ret = $this->getFilterValues()[$alias] ?? $default;

        return $ret === '' ? $default : $ret;
    }

    /**
     * @return mixed
     */
    private function getFilterValidatedValue(string $alias, $default = null)
    {
        return isset($this->$alias) && !empty($this->$alias) ? $this->$alias : $default;
    }

    private function isSetFilter(string $alias): bool
    {
        return isset($this->getFilterValues()[$alias]);
    }

    private function isEmptyFilter(string $alias): bool
    {
        return empty($this->getFilterValues()[$alias]);
    }

    public function dropDown(FilterDropdown $filter, string $attribute): string
    {
        return Dropdown::element(
            $this,
            $attribute,
            $filter->items(),
            $filter->label($this->{$attribute})
        );
    }

    public function htmlDropDownCustom(string $field, array $items, string $selected = null): string
    {
        return Dropdown::element($this, $field, $items, $selected);
    }

    public function htmlDropDownList(string $name, $items = [], $options = [], $default = null): string
    {
        $options['class'] = ($options['class'] ?? '') . ' ' . $this->elementClass;

        return Html::dropDownList(
            $this->getFilterInputName($name),
            $this->getFilterValidatedValue($name, $default),
            $items,
            $options
        );
    }

    public function htmlTextInput(string $name, array $options = [], $default = ''): string
    {
        $options['class'] = ($options['class'] ?? '') . ' ' . $this->elementClass;

        return Html::textInput(
            $this->getFilterInputName($name),
            $this->getFilterValidatedValue($name, $default),
            $options
        );
    }

    public function datePickerFilter(string $field, string $placeHolder = ''): string
    {
        return DatePicker::widget([
            'id'            => 'dp_' . $this->getFormAlias() . '_' . $field,
            'name'          => $this->getFilterInputName($field),
            'layout'        => '{input}{remove}',
            'value'         => substr(trim($this->getFilterValidatedValue($field)), 0, 10),
            'type'          => DatePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'format'         => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'autoclose'      => true,
            ],
            'options'       => [
                'placeholder'  => $placeHolder,
                'class'        => $this->elementClass,
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function datePickerFilterPeriod(array $conf): string
    {
        $ret = '';
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->datePickerFilter($field, $placeholder);
        }

        return $ret;
    }

    public function dateTimePickerFilterActive(string $field, string $placeHolder = '', bool $start = true): string
    {
        return DateTimePicker::widget([
            'id'            => 'dp_' . $this->getFormAlias() . '_' . $field,
            'name'          => $this->getFilterInputName($field),
            'layout'        => '{input}{remove}',
            'value'         => substr(trim($this->getFilterValidatedValue($field)), 0, 20),
            'type'          => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'format'         => 'yyyy-mm-dd hh:ii:' . ($start ? '00' : '59'),
                'minuteStep'     => 1,
                'todayHighlight' => true,
                'autoclose'      => true,
            ],
            'options'       => [
                'placeholder'  => $placeHolder,
                'class'        => $this->elementClass . ' date-time-picker',
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function dateTimePickerFilterPeriod(array $conf): string
    {
        $ret = '';
        $cnt = 1;
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->dateTimePickerFilterActive($field, $placeholder, $cnt === 1);
            $cnt++;
        }

        return $ret;
    }

    public function pairTextFilter(array $conf): string
    {
        $ret = '';
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->htmlTextInput($field, ['placeholder' => $placeholder]);
        }

        return $ret;
    }

    public function htmlCheckBox(string $attribute, string $label): string
    {
        return CheckboxWidget::widget(
            CheckBox::loadOption(
                false,
                [
                    'model'     => $this,
                    'attribute' => $attribute,
                    'type'      => CheckboxWidget::TYPE_CHECKBOX,
                    'style'     => CheckboxWidget::STYLE_PRIMARY,
                    'options'   => [
                        'label' => $label,
                        'class' => 'checkbox-list-container',
                    ],
                ]
            )
        );
    }

    public function htmlRadioList(string $attribute, array $list, string $label): string
    {
        return CheckboxWidget::widget([
            'model'          => $this,
            'attribute'      => $attribute,
            'type'           => CheckboxWidget::TYPE_RADIO,
            'style'          => CheckboxWidget::STYLE_PRIMARY,
            'list'           => $list,
            'options'        => [
                'label' => $label,
                'class' => 'checkbox-list-container text-nowrap small text-center',
            ],
            'wrapperOptions' => [
                'class' => 'checkbox-inline checkbox-inline-list-slim ',
            ],
        ]);
    }

    public function filterCreateLink(string $title, string $uri, array $searchParams = [], array $options = []): string
    {
        $uriParams = [];
        if (!empty($searchParams)) {
            foreach ($searchParams as $param => $value) {
                $uriParams[$this->getFilterInputName($param)] = $value;
            }
        }

        return static::createUrl($title, $uri, $uriParams, $options);
    }

    public static function filterLink(string $title, string $uri, array $searchParams = [], array $options = []): string
    {
        $uriParams = [];
        if (!empty($searchParams)) {
            foreach ($searchParams as $param => $value) {
                $uriParams[static::filterPrefix() . '[' . $param . ']'] = $value;
            }
        }

        return static::createUrl($title, $uri, $uriParams, $options);
    }

    private static function createUrl(string $title, string $uri, array $uriParams, array $options = []): string
    {
        return Html::a(
            $title,
            Yii::$app->getUrlManager()->createUrl(array_merge([$uri], $uriParams)),
            $options
        );
    }
}
