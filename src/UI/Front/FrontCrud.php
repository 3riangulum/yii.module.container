<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front;

use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;

class FrontCrud extends FrontBase
{
    public function init(): void
    {
        parent::init();

        $allowDelete = $this->loadRouter()->isAllowed(RouterBase::ACTION_DELETE);

        $this->actionConfig = FrontConfig::builder([
            'gridClass' => $this->gridClass,
            'router'    => $this->loadRouter(),
            'delete'    => $allowDelete ? RouterBase::ACTION_DELETE : ''
        ])
            ->buildGrid(self::ALIAS_GRID, RouterBase::ACTION_INDEX)
            ->buildPopup(self::ALIAS_EDITOR, RouterBase::ACTION_EDIT)
            ->buildPopup(self::ALIAS_CREATOR, RouterBase::ACTION_CREATE, true, false)
            ->buildPopup(self::ALIAS_DUPLICATOR, RouterBase::ACTION_DUPLICATE, true, false)
            ->buildPopup(self::ALIAS_VIEWER, RouterBase::ACTION_VIEW, false, false)
            ->buildPopup(self::ALIAS_ERASER, RouterBase::ACTION_DELETE)
            ->export();
    }

    public function viewer()
    {
        return $this->popupLoad(self::ALIAS_VIEWER);
    }

    public function editor()
    {
        return $this->popupLoad(self::ALIAS_EDITOR);
    }

    public function creator()
    {
        return $this->popupLoad(self::ALIAS_CREATOR);
    }

    public function eraser()
    {
        return $this->popupLoad(self::ALIAS_ERASER);
    }

    public function duplicator()
    {
        return $this->popupLoad(self::ALIAS_DUPLICATOR);
    }
}
