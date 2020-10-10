<?php


namespace Aigletter\LaravelMenu;

use Aigletter\LaravelMenu\Contracts\MenuItemRepositoryInterface;
use Aigletter\LaravelMenu\Contracts\MenuRepositoryInterface;
use Aigletter\LaravelMenu\Models\MenuItemModel;
use Aigletter\LaravelMenu\Models\MenuModel;
use Aigletter\LaravelMenu\Repository\EloquentMenuItemRepository;
use Aigletter\LaravelMenu\Repository\EloquentMenuRepository;
use Aigletter\Menu\Builder\MenuBuilder;
use Illuminate\Support\ServiceProvider;

class LaravelMenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        //$this->mergeConfigFrom(__DIR__. '/../config/laravelmenu.php', 'laravelmenu');

        $this->app->bind(MenuRepositoryInterface::class, function($app){
            return new EloquentMenuRepository(new MenuModel());
        });

        $this->app->bind(MenuItemRepositoryInterface::class, function($app){
            return new EloquentMenuItemRepository(new MenuItemModel());
        });

        $this->app->singleton(LaravelMenu::class, function($app){
            $menuRepository = $app->get(MenuRepositoryInterface::class);
            $menuItemRepository = $app->get(MenuItemRepositoryInterface::class);
            return new LaravelMenu($menuRepository, $menuItemRepository, new MenuBuilder());
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.  '/../database/migrations');
    }
}