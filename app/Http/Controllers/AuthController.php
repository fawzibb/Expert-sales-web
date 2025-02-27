<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
        public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->active_to && Carbon::parse($user->active_to)->isPast()) {
                $user->update(['active' => 0]); // تعطيل المستخدم
                Auth::logout();
                return response()->json(['message' => 'Your account has expired. Please contact support.'], 403);
            }

            if (!$user->active) {
                return response()->json(['message' => 'Your account is inactive.'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

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
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
