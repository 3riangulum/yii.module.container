<?php

namespace Triangulum\Yii\ModuleContainer\UI\Front;

use Triangulum\Yii\ModuleContainer\System\Db\RepositoryBase;
use Triangulum\Yii\ModuleContainer\UI\Access\RouterBase;
use Triangulum\Yii\ModuleContainer\UI\Front\Element\ElementGrid;

class FrontCrud extends FrontBase
{
    public string $title           = '';
    public string $titleGrid       = '';
    public string $titleEditor     = '';
    public string $titleCreator    = '';
    public string $titleDuplicator = '';
    public string $titleEraser     = '';

    private $creator    = null;
    private $editor     = null;
    private $viewer     = null;
    private $duplicator = null;
    private $eraser     = null;

    public function init(): void
    {
        parent::init();

        $this->loadDefaultActionTitles();

        $allowDelete = $this->loadRouter()->isAllowed(RouterBase::ACTION_DELETE);

        $this->actionConfig = FrontConfig::builder([
            'gridClass' => $this->gridClass,
            'router'    => $this->loadRouter(),
            'delete'    => $allowDelete ? RouterBase::ACTION_DELETE : '',
        ])
            ->buildGrid(self::ALIAS_GRID, RouterBase::ACTION_INDEX, $this->titleGrid)
            ->buildPopup(self::ALIAS_EDITOR, RouterBase::ACTION_EDIT)
            ->buildPopup(self::ALIAS_CREATOR, RouterBase::ACTION_CREATE, true, false)
            ->buildPopup(self::ALIAS_DUPLICATOR, RouterBase::ACTION_DUPLICATE, true, false)
            ->buildPopup(self::ALIAS_VIEWER, RouterBase::ACTION_VIEW, false, false)
            ->buildPopup(self::ALIAS_ERASER, RouterBase::ACTION_DELETE)
            ->export();
    }

    public function grid(array $searchParams = [])
    {
        $grid = ElementGrid::builder($this->actionConfig()[self::ALIAS_GRID]);

        if ($this->editor()->isAllowed()) {
            $grid->clickEventSet($this->editor(), self::ALIAS_EDITOR);
        } elseif ($this->viewer()->isAllowed()) {
            $grid->clickEventSet($this->viewer(), self::ALIAS_VIEWER);
        }

        if ($this->duplicator()->isAllowed()) {
            $grid->clickEventSet($this->duplicator(), self::ALIAS_DUPLICATOR);
            $grid->actionColumnSet([$this->duplicator()]);
        }

        if (!empty($this->searchComponent)) {
            $searchModel = $this->loadModuleComponent($this->searchComponent);
            $grid->dataProviderSet($searchModel->search($searchParams));
            if ($this->gridFilterEnable) {
                $grid->searchModelSet($searchModel);
            }

            if (!empty($grid->sortableAction) && $this->router->isAllowed($grid->sortableAction)) {
                $grid->sortableAction = $this->router->route($grid->sortableAction);
            }
        }

        return $grid;
    }

    private function loadDefaultActionTitles(): void
    {
        $this->titleGrid = $this->title;
        $this->titleCreator = 'Creation.' . $this->title;
        $this->titleEditor = 'Redaction.' . $this->title;
        $this->titleDuplicator = 'Duplication.' . $this->title;
        $this->titleEraser = 'Deletion.' . $this->title;
    }

    public function viewer()
    {
        if (null === $this->viewer) {
            $this->viewer = $this
                ->popupLoad(self::ALIAS_VIEWER)
                ->setTitle($this->title);
        }

        return $this->viewer;
    }

    public function editor(RepositoryBase $repository = null)
    {
        if (null === $this->editor) {
            $this->editor = $this
                ->popupSetup(self::ALIAS_EDITOR, $repository)
                ->setTitle($this->titleEditor);
        }

        return $this->editor;
    }

    public function creator(RepositoryBase $repository = null)
    {
        if (null === $this->creator) {
            $this->creator = $this
                ->popupSetup(self::ALIAS_CREATOR, $repository)
                ->setTitle($this->titleCreator);
        }

        return $this->creator;
    }

    public function eraser()
    {
        if (null === $this->eraser) {
            $this->eraser = $this
                ->popupSetup(self::ALIAS_ERASER)
                ->setTitle($this->titleEraser);
        }

        return $this->eraser;
    }

    public function duplicator(RepositoryBase $repository = null)
    {
        if (null === $this->duplicator) {
            $this->duplicator = $this
                ->popupSetup(self::ALIAS_DUPLICATOR, $repository)
                ->setTitle($this->titleDuplicator);
        }

        return $this->duplicator;
    }

    private function popupSetup(string $alias, RepositoryBase $repository = null)
    {
        $element = $this->popupLoad($alias);
        if ($repository && null !== $repository->entity()) {
            $element
                ->setPk($repository->entityPk())
                ->setHasError($repository->entity()->hasErrors());
        }

        return $element;
    }
}
