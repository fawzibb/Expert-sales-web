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

            // Check if active_to has expired
            if ($user->active_to && Carbon::parse($user->active_to)->isPast()) {
                $user->update(['active' => 0]); // Deactivate the user
                Auth::logout();
                return response()->json(['message' => 'Your account has expired. Please contact support.'], 403);
            }

            // Check if user is active
            if (!$user->active) {
                return response()->json(['message' => 'Your account is inactive.'], 403);
            }

            // Generate API token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'active' => $user->active,
                'active_to' => $user->active_to, // Send active_to date to frontend
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
