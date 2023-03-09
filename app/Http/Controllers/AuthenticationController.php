<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    //
    public function login(Request $request)
    {
        // $this->authorize('verified');
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = request(['email', 'password']);

        if (!auth('web')->attempt($credentials)) {
            return response()->json([
                'message' => 'Login error!',
                'status' => 'UNAUTHORIZED',
                'status_code' => 401,
            ]);
        }

        $user = User::where('email', $request->email)->first();

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'status' => 'OK',
            'status_code' => 200,
            'token' => $tokenResult,
            'user' => $user,
        ]);
    }

    public function getuser(Request $request)
    {
        $userID = $request->user();

        if (!$userID) {

            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "User not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $status_code = 200;

        return response([
            'message' => "Current logged in user.",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $userID
        ], $status_code);
    }

    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        session()->forget('access_provider_token');

        $status_code = 200;

        return response([
            'message' => "Logout successful.",
            'status' => 'OK',
            'status_code' => $status_code,
        ], $status_code);
    }

}
