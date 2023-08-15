<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password']),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }

    // login user
    public function login(Request $request)
    {
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::attempt($attrs)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 403);
        }

        return response()->json([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ], 200);
    }

    // logout user
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logout success.',
        ]);
    }

    // get user detail
    public function user()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }

    // update user
    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image,
        ]);

        return response()->json([
            'message' => 'Updated successfully',
            'user' => auth()->user(),
        ], 200);
    }
}
