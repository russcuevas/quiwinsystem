@extends('layouts.app')

@section('title', 'Login - Quiwin')

@section('content')
<div style="max-width: 460px; margin: 1rem auto;">
    <div class="glass-card" style="padding: clamp(1.25rem, 4vw, 2.5rem); border: 1px solid rgba(99, 102, 241, 0.25); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
        
        <!-- Header / Logo -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <img src="{{ asset('images/logo.jpg') }}" alt="Quiwin Logo" style="width: 68px; height: 68px; margin: 0 auto 0.85rem; border-radius: 18px; object-fit: cover; box-shadow: 0 0 28px rgba(99, 102, 241, 0.6); border: 2px solid rgba(99, 102, 241, 0.5); display: block;">
            <h2 style="font-size: clamp(1.4rem, 4vw, 1.75rem); font-weight: 800; color: #fff; letter-spacing: -0.5px;">Welcome to Quiwin</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.2rem;">Sign in to compete in high-stakes quiz battles</p>
        </div>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">Email Address</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="email" name="email" id="loginEmail" value="{{ old('email') }}" class="form-input" style="padding-left: 2.75rem;" placeholder="name@domain.com" required autofocus>
                </div>
                @error('email')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label style="font-size: 0.82rem; font-weight: 600; color: #cbd5e1;">Password</label>
                </div>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="password" name="password" id="loginPassword" class="form-input" style="padding-left: 2.75rem;" placeholder="••••••••" required>
                </div>
                @error('password')
                    <span style="color: #fb7185; font-size: 0.78rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: #94a3b8; cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: #6366f1; width: 16px; height: 16px; border-radius: 4px;">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 700;">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In to Play
            </button>
        </form>

        <!-- Quick Demo Login Helpers -->
        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.08);">
            <p style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 0.65rem; text-align: center;">
                Quick Demo Login
            </p>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline" style="flex: 1; min-width: 130px; font-size: 0.78rem; padding: 0.45rem;" onclick="fillLogin('player@quiwin.com', 'password123')">
                    <i class="fa-solid fa-user text-cyan-400"></i> Player Demo
                </button>
                <button type="button" class="btn btn-outline" style="flex: 1; min-width: 130px; font-size: 0.78rem; padding: 0.45rem;" onclick="fillLogin('admin@quiwin.com', 'admin123')">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> Admin Demo
                </button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 1.25rem;">
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Don't have an account? 
                <a href="{{ route('register') }}" style="color: #818cf8; font-weight: 700; text-decoration: none;">
                    Register (+200 PTS)
                </a>
            </p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillLogin(email, pwd) {
        document.getElementById('loginEmail').value = email;
        document.getElementById('loginPassword').value = pwd;
    }
</script>
@endpush
