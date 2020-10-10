<?php


namespace Aigletter\LaravelMenu\Models;


use Illuminate\Database\Eloquent\Model;

class MenuItemModel extends Model
{
    protected $table = 'menu_items';

    public function items()
    {
        return $this->hasMany(static::class,'parent_id','id');
    }
}