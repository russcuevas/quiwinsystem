@extends('layouts.app')

@section('title', 'Register - Quiwin')

@section('content')
<div style="max-width: 480px; margin: 1rem auto;">
    <div class="glass-card" style="padding: clamp(1.25rem, 4vw, 2.5rem); border: 1px solid rgba(16, 185, 129, 0.25); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
        
        <!-- Registration Bonus Banner -->
        <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(6, 182, 212, 0.2)); border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 0.85rem; padding: 0.85rem; text-align: center; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.45rem; color: #34d399; font-weight: 800; font-size: 1rem;">
                <i class="fa-solid fa-gift fa-bounce"></i> Welcome Bonus: +200 PTS!
            </div>
            <p style="color: #a7f3d0; font-size: 0.8rem; margin-top: 0.2rem;">
                Register today & get 200 PTS + unique Coupon Code once approved by Admin!
            </p>
        </div>

        <div style="text-align: center; margin-bottom: 1.25rem;">
            <img src="{{ asset('images/logo.jpg') }}" alt="Quiwin Logo" style="width: 68px; height: 68px; margin: 0 auto 0.85rem; border-radius: 18px; object-fit: cover; box-shadow: 0 0 28px rgba(16, 185, 129, 0.6); border: 2px solid rgba(16, 185, 129, 0.5); display: block;">
            <h2 style="font-size: clamp(1.4rem, 4vw, 1.75rem); font-weight: 800; color: #fff;">Create Player Account</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.2rem;">Join the battle and climb the global leaderboards</p>
        </div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">Username / Player Name</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user-astronaut" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" style="padding-left: 2.75rem;" placeholder="e.g. TriviaMaster99" required autofocus>
                </div>
                @error('name')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">Email Address</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" style="padding-left: 2.75rem;" placeholder="name@domain.com" required>
                </div>
                @error('email')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="password" name="password" class="form-input" style="padding-left: 2.75rem;" placeholder="At least 6 characters" required>
                </div>
                @error('password')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">Confirm Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-shield-check" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="password" name="password_confirmation" class="form-input" style="padding-left: 2.75rem;" placeholder="Repeat password" required>
                </div>
            </div>

            <!-- Referral / Coupon Code Input (Optional) -->
            <div style="margin-bottom: 1.25rem; background: rgba(99, 102, 241, 0.08); border: 1px dashed rgba(99, 102, 241, 0.35); border-radius: 0.85rem; padding: 0.85rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #a5b4fc; margin-bottom: 0.35rem;">
                    <i class="fa-solid fa-ticket text-amber-400"></i> Referral / Coupon Code (Optional)
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-tag" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #818cf8;"></i>
                    <input type="text" name="referral_code" value="{{ old('referral_code', request('ref')) }}" class="form-input" style="padding-left: 2.75rem; text-transform: uppercase; font-family: monospace; font-weight: 700; letter-spacing: 1px;" placeholder="e.g. QUI-XXXXXX">
                </div>
                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem;">
                    Have a friend's coupon code? Enter it here to help them earn 1,000 PTS on their 5/5 Quest!
                </div>
                @error('referral_code')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fa-solid fa-user-plus"></i> Submit Registration
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.25rem;">
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Already registered? 
                <a href="{{ route('login') }}" style="color: #818cf8; font-weight: 700; text-decoration: none;">
                    Sign in here
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
