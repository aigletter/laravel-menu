<?php


namespace Aigletter\LaravelMenu\Repository;


use Aigletter\LaravelMenu\Contracts\MenuRepositoryInterface;

class EloquentMenuRepository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}