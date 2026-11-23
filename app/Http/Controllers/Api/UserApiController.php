<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserApiController extends Controller
{
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'VALIDATION_ERROR',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'error_code' => 'INVALID_CREDENTIALS',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
        ], 200);
    }

    public function index()
    {
        return response()->json(
            User::with('user')
                ->active()
                ->latest()
                ->paginate(20)
        );
    }
}