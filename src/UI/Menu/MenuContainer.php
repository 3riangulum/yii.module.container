<?php

namespace Triangulum\Yii\ModuleContainer\UI\Menu;

use Triangulum\Yii\ModuleContainer\UI\BaseObjectUI;

class MenuContainer extends BaseObjectUI
{
    public string $title = '';
    public string $icon  = ' ';

    /**
     * @var MenuItem[]|array
     */
    public array $itemList = [];

    public function addItem(MenuItem $menuItem): self
    {
        $this->itemList[] = $menuItem;

        return $this;
    }

    /**
     * @param MenuItem[] $menuItemList
     * @return $this
     */
    public function addItemList(array $menuItemList): self
    {
        $this->itemList = array_merge($this->itemList, $menuItemList);

        return $this;
    }

    public function export(): array
    {
        $menu = [
            'label'   => $this->title,
            'alias'   => [],
            'visible' => false,
            'icon'    => $this->icon,
            'items'   => [],
        ];

        $aliasList = [];
        $items = [];
        $visible = false;
        foreach ($this->itemList as $menuItem) {
            if ($menuItem->isAllowed()) {
                $visible = true;
            }

            $aliasList[] = $menuItem->alias();
            $menu['items'][] = $menuItem->export();
            $menu['items'][] = $this->divider($visible);
        }

        $menu['alias'] = array_unique($aliasList);
        $menu['visible'] = $visible && count($aliasList);

        return $menu;
    }

    protected function divider(bool $visible = true): array
    {
        return [
            'label'       => '<hr class="hr-slim-white-dotted">',
            'linkOptions' => ['role' => 'presentation', 'class' => 'divider '],
            'visible'     => $visible,
        ];
    }

}
