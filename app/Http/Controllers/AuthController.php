<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('user.home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact an admin.']);
            }

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.home'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('user.home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $welcomeBonus = (int) \App\Models\GameSetting::getVal('welcome_bonus', 200);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'points' => $welcomeBonus, // Dynamic welcome bonus
            'is_active' => true,
        ]);

        // Record initial welcome bonus transaction
        PointTransaction::create([
            'user_id' => $user->id,
            'game_session_id' => null,
            'type' => 'register_bonus',
            'amount' => $welcomeBonus,
            'balance_after' => $welcomeBonus,
            'description' => "Welcome registration bonus (+{$welcomeBonus} Quiwin Points)",
        ]);

        Auth::login($user);

        return redirect()->route('user.home')->with('success', "Welcome to Quiwin! {$welcomeBonus} bonus points have been added to your account.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
