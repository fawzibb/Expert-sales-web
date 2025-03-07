<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    
    protected $table = 'orders';


    protected $fillable = [
        'user_id',
        'name',
        'price',
        'description'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_order', 'order_id', 'item_id')
                    ->withPivot('quantity', 'price');
    }


    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->pivot->price * $item->pivot->quantity;
        });
    }


    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('pivot.quantity');
    }
}
