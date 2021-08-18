<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html\Dropdown;

use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;

class FilterDropdown extends BaseObjectUI
{
    public array  $itemMap             = [];
    public string $labelContainerClass = 'text-center';

    public function items(): array
    {
        $ret = [];
        foreach ($this->itemMap as $status => $label) {
            $ret[] = [
                'id'   => $status,
                'text' => $this->label($status),
            ];
        }

        return $ret;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function label($value): string
    {
        return '<div class="' . $this->labelContainerClass . '">' . $this->labelText($value) . '</div>';
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function labelText($value): ?string
    {
        return $this->itemMap[$value] ?? null;
    }
}
