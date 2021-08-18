<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

final class ButtonConfig
{
    private array   $class        = ['margin5px'];
    private array   $url          = [];
    private string  $title        = 'Button';
    private array   $data         = [];
    private ?string $tooltipTitle = null;
    private bool    $pjax         = false;

    public function classes(array $class): self
    {
        $this->class = array_merge($this->class, $class);

        return $this;
    }

    public function getClass(): array
    {
        return $this->class;
    }

    public function url(array $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): array
    {
        return $this->url;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function tooltipTitle(string $title): self
    {
        $this->tooltipTitle = $title;

        return $this;
    }

    public function getTooltipTitle(): ?string
    {
        return $this->tooltipTitle;
    }

    public function usePjax(): self
    {
        $this->pjax = true;

        return $this;
    }

    public function hasPjax(): bool
    {
        return $this->pjax;
    }

    public function exportOptions(): array
    {
        $opt = [
            'class' => $this->getClass(),
            'data'  => $this->getData(),
        ];

        if ($tooltipTitle = $this->getTooltipTitle()) {
            $opt['title'] = $tooltipTitle;
            $opt['class'][] = Tooltip::TOOLTIP_CLASS;
            $opt['data']['placement'] = Tooltip::TOOLTIP_PLACEMENT_TOP;
            $opt['data']['toggle'] = Tooltip::TOOLTIP_DATA_TOGGLE;
        }

        if ($this->hasPjax()) {
            $opt['data']['pjax'] = 1;
        }

        return $opt;
    }
}
