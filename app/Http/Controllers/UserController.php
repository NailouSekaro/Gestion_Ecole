<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Session;

class UserController extends Controller
{
    public function login()
    {
        return view('user.login');
    }

    public function handelogin(UserRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error_message', 'Paramètre de connexion invalide');
        }
    }

    

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('login');
    }
}
