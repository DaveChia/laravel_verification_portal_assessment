<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterNewUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterNewUserRequest $request) : Response
    {
        try {
            $validated = $request->validated();

            $user = new User;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'error' => 'unexpected_error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'result' => true,
            'message' => 'User Created Successfully',
        ], Response::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request) : Response
    {
        try {
            $request->validated();

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'result' => false,
                    'error' => 'invalid_credentials',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where('email', $request->email)->first();

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'unexpected_error'
            ], RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'result' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], RESPONSE::HTTP_OK);
    }
}