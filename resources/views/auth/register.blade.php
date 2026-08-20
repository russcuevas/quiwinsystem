@extends('layouts.app')

@section('title', 'Register - Quiwin')

@section('content')
<div style="max-width: 480px; margin: 1.5rem auto;">
    <div class="glass-card" style="padding: 2.5rem; border: 1px solid rgba(16, 185, 129, 0.25); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
        
        <!-- Registration Bonus Banner -->
        <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(6, 182, 212, 0.2)); border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 1rem; padding: 1rem; text-align: center; margin-bottom: 1.75rem;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #34d399; font-weight: 800; font-size: 1.1rem;">
                <i class="fa-solid fa-gift fa-bounce"></i> Welcome Bonus: +200 PTS!
            </div>
            <p style="color: #a7f3d0; font-size: 0.85rem; margin-top: 0.25rem;">
                Sign up today and get 200 free Quiwin points instantly to play.
            </p>
        </div>

        <div style="text-align: center; margin-bottom: 1.75rem;">
            <h2 style="font-size: 1.75rem; font-weight: 800; color: #fff;">Create Player Account</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Join the battle and climb the global leaderboards</p>
        </div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Username / Player Name</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user-astronaut" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" style="padding-left: 2.75rem;" placeholder="e.g. TriviaMaster99" required autofocus>
                </div>
                @error('name')
                    <span style="color: #fb7185; font-size: 0.8rem; margin-top: 0.35rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Email Address</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" style="padding-left: 2.75rem;" placeholder="name@domain.com" required>
                </div>
                @error('email')
                    <span style="color: #fb7185; font-size: 0.8rem; margin-top: 0.35rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="password" name="password" class="form-input" style="padding-left: 2.75rem;" placeholder="At least 6 characters" required>
                </div>
                @error('password')
                    <span style="color: #fb7185; font-size: 0.8rem; margin-top: 0.35rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">Confirm Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-shield-check" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="password" name="password_confirmation" class="form-input" style="padding-left: 2.75rem;" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fa-solid fa-coins"></i> Claim 200 PTS & Start Playing
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Already registered? 
                <a href="{{ route('login') }}" style="color: #818cf8; font-weight: 600; text-decoration: none;">
                    Sign in here
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
