<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
class AuthController extends Controller
{
    
    public function index()
    {
        return view('index');
    }

    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required|min:6',
    ]);

    $user = User::where('username', $request->username)->first();
    
    if (!$user) {
        return response()->json([
            'success' => false,
            'errors' => [
                'username' => ['Username does not exist.'],
            ],
        ], 422);
    }

    if ($request->password !== $user->password) {
        return response()->json([
            'success' => false,
            'errors' => [
                'password' => ['Incorrect password.'],
            ],
        ], 422);
    }
	
    $user->last_login = now();
    $user->save();

    session(['username' => $user->username]);

    return response()->json([
        'success' => true,
        'redirect' => route('dashboard'),
    ]);
}


    public function logout()
    {
        return redirect()->route('index');
    }
}
