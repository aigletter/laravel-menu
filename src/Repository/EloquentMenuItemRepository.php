<?php


namespace Aigletter\LaravelMenu\Repository;


use Aigletter\LaravelMenu\Contracts\MenuItemRepositoryInterface;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getByMenuId($menuId)
    {
        return $this->model->where([
            ['menu_id', '=', $menuId],
            ['level', '=', 1]
        ])->with('items')->get();
    }
}