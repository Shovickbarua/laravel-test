<?php

namespace App\Http\Controllers;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    //
    public function index(Request $request)
    {
        $users = Cache::remember('active_users', 60, function () {
            return User::where('active', 1)->get();
        });
        return response()->json([
            'message' => 'Hello World!',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => 1,
        ]);

        SendWelcomeEmail::dispatch($user);

        if (!$user) {
            return response()->json([
                'message' => 'User creation failed!',
            ], 500);
        }

        if (!$user->active) {
            return response()->json([
                'message' => 'User not active!',
            ], 403);
        }

        return response()->json([
            'message' => 'Active User created successfully!',
            'user' => $user,
        ]);
    }
}
