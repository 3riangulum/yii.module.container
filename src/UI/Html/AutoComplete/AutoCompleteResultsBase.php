<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\AutoComplete;

use yii\base\BaseObject;
use yii\helpers\HtmlPurifier;

abstract class AutoCompleteResultsBase extends BaseObject
{
    public string $term           = '';
    public string $pk;
    public bool   $requestIsValid = false;
    public int    $resultLimit    = 30;

    public function init(): void
    {
        parent::init();
        $this->term = $this->stringPurify($this->term);
        $this->pk = (int)$this->pk;
    }

    abstract public function search(): array;

    abstract public function label($data): string;

    abstract public function loadSelected(): ?string;

    abstract protected function decorate(array $list): array;

    /**
     * @param mixed $id
     * @param mixed $text
     * @return array
     */
    protected static function content($id, $text): array
    {
        return ['id' => $id, 'text' => $text];
    }

    protected function contentEmpty(): array
    {
        return $this->content('', '');
    }

    protected function result(array $content, string $index = 'results'): array
    {
        return [$index => $content];
    }

    protected function resultEmpty(): array
    {
        return $this->result($this->contentEmpty());
    }

    protected function stringPurify(string $value): string
    {
        return trim(HtmlPurifier::process($value));
    }
}
