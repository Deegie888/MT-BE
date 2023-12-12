<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\User\RegistrationRequest;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(RegistrationRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();
            return response()->json([
                'message' => 'Registration complete'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::where('email', $validated['email'])->first();
            if(Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'OK',
                    'uid' => $user->id
                ], 200);
            }

            $errors = ['email' => 'invalid email or password'];

            return response()->json([
                'message' => 'unauthorized',
                'errors' => $errors
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }
}
