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
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function register(RegisterNewUserRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = new User;
            $user->name = $validated['name'];
            $user->email = $validated['name'];
            $user->password = Hash::make($validated['email']);



        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'unexpected_error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'result' => true,
            'message' => 'User Created Successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $validated = $request->validated();

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'result' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where('email', $request->email)->first();


        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected_error'
            ], RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'result' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], RESPONSE::HTTP_OK);
    }
}