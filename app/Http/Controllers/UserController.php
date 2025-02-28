<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    // Get a single user by ID
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // Store a new user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'active' => 'boolean',
            'active_to' => 'date|nullable',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']); // Hash password

        if (!isset($validatedData['active_to'])) {
            $validatedData['active_to'] = Carbon::now()->addDays(14)->format('Y-m-d'); // 7 أيام من الآن
        } else {
            $validatedData['active_to'] = Carbon::parse($validatedData['active_to'])->format('Y-m-d');
        }

        // إضافة dd() لمراجعة التاريخ الذي يتم حسابه
       // dd($validatedData['active_to']); // هذا سيساعدك على التحقق من التاريخ الذي يتم حسابه

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }


    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'active' => 'boolean',
            'active_to' => 'date|nullable',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // إذا تم إرسال active_to، نقوم بتحويله للتنسيق المطلوب
        if (isset($validatedData['active_to'])) {
            $validatedData['active_to'] = Carbon::parse($validatedData['active_to'])->format('Y-m-d');
        } else {
            // إذا لم يتم إدخال قيمة لـ active_to، نقوم بتعيينه 7 أيام من الآن
            $validatedData['active_to'] = Carbon::now()->addDays(7)->format('Y-m-d');
        }

        $user->update($validatedData);

        return response()->json($user);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }



}
