<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات (اختياري إذا كان اسم الجدول يتبع القواعد القياسية)
    protected $table = 'orders';

    // تحديد الأعمدة التي يمكن ملؤها (mass assignable)
    protected $fillable = [
        'user_id',
        'name',
        'price',
        'description'
    ];

    // العلاقة مع موديل User (كل Order ينتمي إلى User واحد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع موديل Item (Order يمكن أن يحتوي على العديد من الـ Items)
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_order', 'order_id', 'item_id')
                    ->withPivot('quantity', 'price'); // إحضار الكمية والسعر من جدول الرابط
    }

    // حساب إجمالي السعر للطلب بناءً على العناصر المرتبطة
    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->pivot->price * $item->pivot->quantity;
        });
    }

    // حساب إجمالي الكمية للطلب
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('pivot.quantity');
    }
}
