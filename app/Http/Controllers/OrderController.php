<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
{
    $orders = Order::with('items')->where('user_id', $request->user()->id)->get()->map(function ($order) {
        return [
            'id' => $order->id,
            'name' => $order->name,
            'total_price' => $order->total_price,
            'total_quantity' => $order->total_quantity,
            'created_at' => $order->created_at,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->name,
                    'price' => $item->pivot->price,
                    'quantity' => $item->pivot->quantity,
                ];
            })
        ];
    });

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'items' => 'required|array',
        'items.*.id' => 'exists:items,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    $totalPrice = 0;
    $orderItems = [];

    foreach ($request->items as $itemData) {
        $item = Item::find($itemData['id']);

        // التحقق من الكمية في المخزون
        if ($item->stock <= 0) {
            return response()->json(['message' => 'Out of Stock for item: ' . $item->name], 400);
        } elseif ($item->stock < $itemData['quantity']) {
            return response()->json(['message' => 'Not enough stock for item: ' . $item->name], 400);
        }

        // تحديث المخزون بعد الخصم
        $item->stock -= $itemData['quantity'];
        $item->save();

        // حساب السعر الإجمالي
        $totalPrice += $item->price * $itemData['quantity'];

        // تجهيز بيانات الطلب
        $orderItems[] = [
            'item_id' => $item->id,
            'price' => $item->price,  // السعر يتم جلبه مباشرة من العنصر
            'quantity' => $itemData['quantity'],
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    // إنشاء الطلب بعد التأكد من وجود الكمية الكافية
    $order = $request->user()->orders()->create([
        'name' => $request->name,
        'price' => $totalPrice,  // استخدام السعر الإجمالي
        'description' => $request->description,
    ]);

    // ربط الطلب بالعناصر
    foreach ($orderItems as &$orderItem) {
        $orderItem['order_id'] = $order->id;
    }
    DB::table('item_order')->insert($orderItems);

    return response()->json(['message' => 'Successful', 'order' => $order->load('items')], 201);
}

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

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }

    public function getOrders(Request $request)
{
    $user = auth()->user();
    $query = $user->orders()->with('items');

    if ($request->has('start_date') && $request->has('end_date')) {
        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date . ' 23:59:59';
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    $orders = $query->get();
    return response()->json($orders);
}

}
