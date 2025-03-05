<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

    public function index()
    {
        $items = Auth::user()->items;
        return response()->json($items);
    }

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

    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
