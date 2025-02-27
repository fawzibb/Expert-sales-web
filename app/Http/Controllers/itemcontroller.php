<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the user's items.
     */
    public function index()
    {
        // استرجاع العناصر الخاصة بالمستخدم
        $items = Auth::user()->items;

        // تمرير البيانات إلى الـ View
        return response()->json($items);
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'stock' => 'nullable|integer',
        ]);

        $item = Auth::user()->items()->create($request->all());
        return response()->json($item, 201);
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'stock' => 'nullable|integer',
        ]);

        $item->update($request->all());
        $item->stock = $request->stock;
        $item->save();
        return response()->json($item);
    }

    /**
     * Remove the specified item.
     */
    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
