<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show_users()
    {
        return response()->json(User::all());
    }


    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }


    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'active' => 'boolean',
            'active_to' => 'date|nullable',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        if (!isset($validatedData['active_to'])) {
            $validatedData['active_to'] = Carbon::now()->addDays(14)->format('Y-m-d');
        } else {
            $validatedData['active_to'] = Carbon::parse($validatedData['active_to'])->format('Y-m-d');
        }



        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->active_to && Carbon::parse($user->active_to)->isPast()) {
                $user->update(['active' => 0]);
                Auth::logout();
                return response()->json(['message' => 'Your account has expired. Please contact support.'], 403);
            }

            if (!$user->active) {
                return response()->json(['message' => 'Your account is inactive.'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->update(['remember_token' => $token]);
            return response()->json([
                'token' => $token,
                'active' => $user->active,
                'active_to' => $user->active_to,
                'message' => 'Login successful'
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
            auth()->logout();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } else {
            return response()->json(['message' => 'No user logged in'], 401);
        }
    }



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



        $user->update($validatedData);

        return response()->json($user);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }



}
