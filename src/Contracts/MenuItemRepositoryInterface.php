<?php


namespace Aigletter\LaravelMenu\Contracts;


interface MenuItemRepositoryInterface
{
    public function getByMenuId($menuId);
}