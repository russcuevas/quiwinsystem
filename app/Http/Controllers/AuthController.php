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

            if (!Auth::user()->isAdmin() && Auth::user()->isPending()) {
                Auth::logout();
                return back()->withErrors(['email' => '⏳ Your account is currently PENDING Admin approval.']);
            }

            if (!Auth::user()->isAdmin() && Auth::user()->status === 'rejected') {
                Auth::logout();
                return back()->withErrors(['email' => '❌ Your account registration was rejected by an administrator.']);
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
            'referral_code' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check referral code if provided
        $referrerId = null;
        if ($request->filled('referral_code')) {
            $refCode = strtoupper(trim($request->referral_code));
            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            } else {
                return back()->withErrors(['referral_code' => 'The referral / coupon code entered does not exist. Please check and try again or leave blank.'])->withInput();
            }
        }

        // Generate unique referral code for this new player
        do {
            $uniqueCode = 'QUI-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (User::where('referral_code', $uniqueCode)->exists());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'pending', // Requires admin approval before login & bonus grant
            'referral_code' => $uniqueCode,
            'referred_by' => $referrerId,
            'points' => 0, // Points are awarded when approved by Admin
            'is_active' => true,
        ]);

        return redirect()->route('login')->with('success', "🎉 Registration successful! Your account is now PENDING Admin Approval. Once approved by the administrator, you will receive your 200 welcome points and your coupon code ({$uniqueCode}) will be active!");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
