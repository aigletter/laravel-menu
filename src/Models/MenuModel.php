<?php


namespace Aigletter\LaravelMenu\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MenuModel extends Model
{
    protected $table = 'menus';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    static public function getMenu(string $name): ?ModelMenuInterface
    {
        $menu = static::where('name', $name)->first();
        return $menu;
    }

    /*public function items()
    {
        return $this->hasMany(
            config('laravelmenu.model.item.class'),
            config('laravelmenu.model.item.key_foreign'),
            config('laravelmenu.model.menu.key_local')
        );
    }*/

    public function getItems()
    {
        return $this->items;
        //return $this->hasMany(config('laravelmenu.model.item.class'), );
    }

    public function items()
    {
        return $this->hasMany(MenuItemModel::class, 'menu_id')->where('level', 1);
    }

    public function children()
    {
        return $this->hasMany(MenuItemModel::class, 'menu_id');
    }

    public function setItems(Collection $items)
    {
        $this->setRelation('items', $items);
    }

    /*public function getItemsAttribute()
    {
        $items = $this->hasMany(
            ItemModel::class,
            $this->menuItemModel->getMenuForeignKey(),
            $this->menuModel->getKeyName()
        )->orderBy('order')->get();
        //$model->setRelation('items', $items);
        //$model->setItems($items);

        return $items;
    }*/
}
