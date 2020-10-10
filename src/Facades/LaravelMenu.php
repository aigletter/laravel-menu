<?php


namespace Aigletter\LaravelMenu\Facades;


use Illuminate\Support\Facades\Facade;

class LaravelMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Aigletter\LaravelMenu\LaravelMenu::class;
    }
}