<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if($user->role == 'admin'){
                return redirect('/admin/home');
            }else{
                return redirect('/user/home');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }


    public function showRegisterForm()
    {
        return view('register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);


        Auth::login($user);

        if($user->role == 'admin'){
            return redirect('/admin/home');
        }else{
            return redirect('/user/home');
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
