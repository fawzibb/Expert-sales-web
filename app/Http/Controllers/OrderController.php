<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // عرض جميع الطلبات الخاصة بالمستخدم
    public function index(Request $request)
{
    $orders = Order::with('items')->where('user_id', $request->user()->id)->get()->map(function ($order) {
        return [
            'id' => $order->id,
            'name' => $order->name,
            'total_price' => $order->total_price, // ستتم إضافة إجمالي السعر عبر accessor
            'total_quantity' => $order->total_quantity, // ستتم إضافة إجمالي الكمية عبر accessor
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->name,
                    'price' => $item->pivot->price, // السعر من جدول الارتباط
                    'quantity' => $item->pivot->quantity, // الكمية من جدول الارتباط
                ];
            })
        ];
    });

        return response()->json($orders);
    }

    // عرض طلب معين
    public function show($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    // إضافة طلب جديد
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'items' => 'required|array',
        'items.*.id' => 'exists:items,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    // إنشاء الطلب
    $order = $request->user()->orders()->create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
    ]);

    // إدراج العناصر في الطلب
    $orderItems = [];
    foreach ($request->items as $itemData) {
        $item = Item::find($itemData['id']);
        for ($i = 0; $i < $itemData['quantity']; $i++) {
            $orderItems[] = [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'price' => $item->price,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }

    // إدراج العناصر في جدول الرابط
    DB::table('item_order')->insert($orderItems);

    return response()->json($order->load('items'), 201);
}

    // تحديث طلب معين
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'exists:items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // تحديث بيانات الطلب
        $order->update($request->only(['name', 'price', 'description']));

        if ($request->has('items')) {
            DB::table('item_order')->where('order_id', $order->id)->delete();
            $orderItems = [];
            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['id']);
                for ($i = 0; $i < $itemData['quantity']; $i++) {
                    $orderItems[] = [
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                        'price' => $item->price,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            DB::table('item_order')->insert($orderItems);
        }

        return response()->json($order->load('items'));
    }

    // حذف طلب
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
