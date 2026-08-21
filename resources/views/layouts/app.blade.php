<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#090d16">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quiwin') - Ultimate Quiz Challenge</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-primary: #090d16;
            --bg-secondary: #0f172a;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-card-border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.35);
            --secondary: #06b6d4;
            --accent-gold: #f59e0b;
            --accent-emerald: #10b981;
            --accent-rose: #f43f5e;
            --accent-purple: #a855f7;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --bottom-nav-height: 64px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
        }

        body {
            background-color: var(--bg-primary);
            background-image:
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(168, 85, 247, 0.06) 0%, transparent 60%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            padding-bottom: 0;
        }

        @media (max-width: 768px) {
            body.has-bottom-nav {
                padding-bottom: calc(var(--bottom-nav-height) + var(--safe-bottom) + 24px);
            }
        }

        /* Ambient animated particles background */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }

        .main-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        @media (max-width: 640px) {
            .main-container {
                padding: 0 0.85rem;
            }
        }

        /* Glassmorphism Navigation */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 13, 22, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--bg-card-border);
            padding: 0.75rem 0;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            text-decoration: none;
            color: #fff;
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }

        .brand-logo .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 16px var(--primary-glow);
            color: #fff;
            font-size: 1.05rem;
        }

        .brand-logo span {
            background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-desktop-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-mobile-actions {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 860px) {
            .nav-desktop-actions {
                display: none;
            }

            .nav-mobile-actions {
                display: flex;
            }
        }

        /* Points Pill Badge */
        .points-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.25));
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-weight: 700;
            color: #fbbf24;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.15);
            transition: all 0.2s ease;
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .points-badge:hover {
            transform: scale(1.03);
            box-shadow: 0 0 18px rgba(245, 158, 11, 0.3);
        }

        /* Buttons & Glass Panels */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--bg-card-border);
            border-radius: 1.25rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            border-radius: 0.85rem;
            padding: 0.65rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            outline: none;
            font-size: 0.95rem;
            min-height: 42px;
            touch-action: manipulation;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .btn-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #090d16;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        /* Form Controls */
        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.75rem 1rem;
            border-radius: 0.85rem;
            color: #fff;
            font-size: 16px;
            /* 16px prevents iOS Safari auto-zoom on input focus */
            outline: none;
            transition: all 0.2s ease;
            min-height: 44px;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        /* Flash Alerts */
        .alert-box {
            padding: 0.9rem 1.25rem;
            border-radius: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
        }

        .alert-error {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.35);
            color: #fb7185;
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(6, 182, 212, 0.35);
            color: #38bdf8;
        }

        /* Modal Overlay & Card */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.82);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 1.25rem;
            width: 100%;
            max-width: 460px;
            padding: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            transform: scale(0.96) translateY(8px);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            overflow-y: auto;
            margin: auto;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1) translateY(0);
        }

        @media (max-width: 480px) {
            .modal-overlay {
                padding: 0.75rem;
            }

            .modal-card {
                padding: 1.15rem 0.95rem;
                border-radius: 1.1rem;
            }
        }

        /* Mobile Side Navigation Drawer */
        .mobile-drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 90;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .mobile-drawer-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            right: -320px;
            width: 290px;
            height: 100%;
            height: 100dvh;
            background: #0b1120;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            z-index: 95;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.8);
            padding: 1.25rem 1rem;
        }

        .mobile-drawer.active {
            right: 0;
        }

        .mobile-drawer-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            min-height: 46px;
        }

        .mobile-drawer-link:hover,
        .mobile-drawer-link:active {
            background: rgba(99, 102, 241, 0.15);
            color: #fff;
        }

        .mobile-drawer-link.active {
            background: rgba(99, 102, 241, 0.25);
            border: 1px solid rgba(99, 102, 241, 0.4);
            color: #818cf8;
        }

        /* Mobile Bottom Floating App Bar */
        .mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(var(--bottom-nav-height) + var(--safe-bottom));
            padding-bottom: var(--safe-bottom);
            background: rgba(9, 13, 22, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid var(--bg-card-border);
            z-index: 45;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            .mobile-bottom-bar {
                display: flex;
                align-items: center;
                justify-content: space-around;
            }
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.35rem 0;
            transition: all 0.2s ease;
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
        }

        .bottom-nav-item i {
            font-size: 1.15rem;
            transition: transform 0.2s ease;
        }

        .bottom-nav-item.active,
        .bottom-nav-item:active {
            color: #818cf8;
        }

        .bottom-nav-item.active i {
            transform: translateY(-2px);
            color: #6366f1;
        }

        .bottom-nav-item.highlight {
            color: #fbbf24;
        }

        .bottom-nav-item.highlight.active {
            color: #f59e0b;
        }

        /* Responsive Table Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.75rem;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 4px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="{{ auth()->check() ? 'has-bottom-nav' : '' }}">
    <!-- Ambient Canvas for Background Particles -->
    <canvas id="ambient-canvas" class="ambient-glow"></canvas>

    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="main-container">
            <div class="nav-inner">
                <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.home')) : route('login') }}"
                    class="brand-logo">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Quiwin" class="brand-logo-img"
                        style="width: 38px; height: 38px; border-radius: 10px; object-fit: cover; box-shadow: 0 0 16px var(--primary-glow); border: 1.5px solid rgba(99, 102, 241, 0.5);">
                    <span>QUIWIN</span>
                </a>

                <!-- Desktop Action Items -->
                <div class="nav-desktop-actions">
                    @auth
                        @if (!auth()->user()->isAdmin())
                            <!-- Points balance -->
                            <div class="points-badge" id="nav-points-badge" title="Your current points balance">
                                <i class="fa-solid fa-coins"></i>
                                <span id="nav-user-points">{{ number_format(auth()->user()->points) }}</span> PTS
                            </div>

                            <!-- Quick Top Up Button -->
                            <button type="button" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem;" onclick="openTopUpModal()">
                                <i class="fa-solid fa-plus-circle text-amber-400"></i> Top-Up
                            </button>

                            <!-- GCash Withdraw Button -->
                            <button type="button" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-color: rgba(16, 185, 129, 0.4); color: #34d399;"
                                onclick="openWithdrawModal()">
                                <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> Withdraw
                            </button>

                            <!-- Mailbox / Notifications Button -->
                            @php
                                $userUnreadMailsCount = auth()->user()->unreadMails()->count();
                            @endphp
                            <button type="button" class="btn btn-outline"
                                style="position: relative; padding: 0.45rem 0.75rem; font-size: 0.85rem; color: #a5b4fc;"
                                onclick="openMailModal()" title="In-Game Mail & Notifications">
                                <i class="fa-solid fa-envelope"></i>
                                @if ($userUnreadMailsCount > 0)
                                    <span
                                        style="position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; border-radius: 9999px; font-size: 0.68rem; font-weight: 800; padding: 0.1rem 0.35rem; min-width: 18px; text-align: center; box-shadow: 0 0 8px rgba(239, 68, 68, 0.8); border: 2px solid #090d16;">
                                        {{ $userUnreadMailsCount }}
                                    </span>
                                @endif
                            </button>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Dashboard
                            </a>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-users text-cyan-400"></i> Players
                            </a>
                            @php
                                $adminPendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
                            @endphp
                            <a href="{{ route('admin.withdrawals') }}" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                                <i class="fa-solid fa-money-bill-wave text-amber-400"></i> Withdrawals
                                @if ($adminPendingWithdrawals > 0)
                                    <span
                                        style="background: #ef4444; color: white; border-radius: 9999px; font-size: 0.68rem; font-weight: 800; padding: 0.1rem 0.45rem; margin-left: 0.3rem;">
                                        {{ $adminPendingWithdrawals }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.questions') }}" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-database text-purple-400"></i> Questions
                            </a>
                            <a href="{{ route('admin.settings') }}" class="btn btn-outline"
                                style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-color: rgba(16, 185, 129, 0.4); color: #34d399;">
                                <i class="fa-solid fa-sliders text-emerald-400"></i> Rules
                            </a>
                        @endif

                        <!-- User Profile Info & Logout -->
                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                            <span
                                style="font-weight: 600; color: #e2e8f0; font-size: 0.88rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fa-solid fa-circle-user text-indigo-400" style="font-size: 1.1rem;"></i>
                                {{ auth()->user()->name }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-outline"
                                    style="padding: 0.45rem 0.75rem; font-size: 0.85rem; color: #f87171;" title="Logout">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fa-solid fa-sparkles"></i> Register
                        </a>
                    @endauth
                </div>

                <!-- Mobile Action Items (Compact & Touch-Friendly) -->
                <div class="nav-mobile-actions">
                    @auth
                        @if (!auth()->user()->isAdmin())
                            <!-- Points badge compact -->
                            <div class="points-badge" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;"
                                onclick="openTopUpModal()">
                                <i class="fa-solid fa-coins"></i>
                                <span id="mobile-nav-points">{{ number_format(auth()->user()->points) }}</span>
                            </div>

                            <!-- Mobile Mail Icon -->
                            @php
                                $mobileUnreadMailsCount = auth()->user()->unreadMails()->count();
                            @endphp
                            <button type="button" class="btn btn-outline"
                                style="position: relative; padding: 0.35rem 0.6rem; font-size: 0.9rem; min-height: 36px;"
                                onclick="openMailModal()">
                                <i class="fa-solid fa-envelope text-indigo-300"></i>
                                @if ($mobileUnreadMailsCount > 0)
                                    <span
                                        style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; border-radius: 9999px; font-size: 0.62rem; font-weight: 800; padding: 0.05rem 0.3rem; min-width: 16px; text-align: center;">
                                        {{ $mobileUnreadMailsCount }}
                                    </span>
                                @endif
                            </button>
                        @endif

                        <!-- Hamburger Menu Trigger -->
                        <button type="button" class="btn btn-outline"
                            style="padding: 0.35rem 0.65rem; font-size: 1.05rem; min-height: 36px;"
                            onclick="toggleMobileDrawer()">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline"
                            style="padding: 0.35rem 0.75rem; font-size: 0.85rem; min-height: 36px;">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary"
                            style="padding: 0.35rem 0.75rem; font-size: 0.85rem; min-height: 36px;">Register</a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Slide-Out Mobile Navigation Drawer -->
    @auth
        <div id="mobileDrawerOverlay" class="mobile-drawer-overlay" onclick="toggleMobileDrawer(false)"></div>
        <div id="mobileDrawer" class="mobile-drawer">
            <div
                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div
                        style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #06b6d4); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem;">
                        <i class="fa-solid fa-circle-user"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #fff; font-size: 1rem;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">
                            {{ auth()->user()->isAdmin() ? 'Administrator' : 'Player' }}</div>
                    </div>
                </div>
                <button type="button" onclick="toggleMobileDrawer(false)"
                    style="background: none; border: none; color: #94a3b8; font-size: 1.35rem; cursor: pointer; padding: 0.25rem;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Drawer Navigation Items -->
            <div style="display: flex; flex-direction: column; gap: 0.35rem; flex: 1; overflow-y: auto;">
                @if (!auth()->user()->isAdmin())
                    <a href="{{ route('user.home') }}"
                        class="mobile-drawer-link {{ request()->routeIs('user.home') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-house text-indigo-400"></i> Hub / Home
                    </a>
                    <button type="button" class="mobile-drawer-link"
                        style="background: none; border: none; text-align: left; width: 100%; cursor: pointer;"
                        onclick="toggleMobileDrawer(false); openTopUpModal();">
                        <i class="fa-solid fa-wallet text-amber-400"></i> Top-Up Points
                    </button>
                    <button type="button" class="mobile-drawer-link"
                        style="background: none; border: none; text-align: left; width: 100%; cursor: pointer;"
                        onclick="toggleMobileDrawer(false); openWithdrawModal();">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> GCash Withdraw
                    </button>
                    <button type="button" class="mobile-drawer-link"
                        style="background: none; border: none; text-align: left; width: 100%; cursor: pointer;"
                        onclick="toggleMobileDrawer(false); openMailModal();">
                        <i class="fa-solid fa-envelope text-purple-400"></i> In-Game Mailbox
                    </button>
                @else
                    <a href="{{ route('admin.dashboard') }}"
                        class="mobile-drawer-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                        class="mobile-drawer-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-users text-cyan-400"></i> Manage Players
                    </a>
                    <a href="{{ route('admin.withdrawals') }}"
                        class="mobile-drawer-link {{ request()->routeIs('admin.withdrawals') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-money-bill-wave text-amber-400"></i> Withdrawals
                        @if (\App\Models\Withdrawal::where('status', 'pending')->count() > 0)
                            <span
                                style="background: #ef4444; color: white; border-radius: 9999px; font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.45rem; margin-left: auto;">
                                {{ \App\Models\Withdrawal::where('status', 'pending')->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.questions') }}"
                        class="mobile-drawer-link {{ request()->routeIs('admin.questions') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-database text-purple-400"></i> Question Bank
                    </a>
                    <a href="{{ route('admin.settings') }}"
                        class="mobile-drawer-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                        onclick="toggleMobileDrawer(false)">
                        <i class="fa-solid fa-sliders text-emerald-400"></i> Rules & Pointing
                    </a>
                @endif
            </div>

            <!-- Drawer Bottom Logout -->
            <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1rem; margin-top: auto;">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline"
                        style="width: 100%; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                        <i class="fa-solid fa-right-from-bracket"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    @endauth

    <!-- Content Area -->
    <main style="flex: 1; padding: 1.5rem 0;">
        <div class="main-container">
            @if (session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if (session('info'))
                <div class="alert-box alert-info">
                    <i class="fa-solid fa-circle-info fa-lg"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div>
                        <strong>Please check the following errors:</strong>
                        <ul style="margin-top: 0.4rem; margin-left: 1.25rem; font-size: 0.9rem;">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Navigation Bar (For quick 1-thumb touch navigation) -->
    @auth
        <nav class="mobile-bottom-bar" aria-label="Mobile Navigation">
            @if (!auth()->user()->isAdmin())
                <a href="{{ route('user.home') }}"
                    class="bottom-nav-item {{ request()->routeIs('user.home') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Hub</span>
                </a>
                <button type="button" class="bottom-nav-item" onclick="openTopUpModal()">
                    <i class="fa-solid fa-wallet text-amber-400"></i>
                    <span>Top-Up</span>
                </button>
                <button type="button" class="bottom-nav-item" onclick="openWithdrawModal()">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i>
                    <span>Withdraw</span>
                </button>
                <button type="button" class="bottom-nav-item" onclick="openMailModal()">
                    <i class="fa-solid fa-envelope text-indigo-400"></i>
                    <span>Mail</span>
                </button>
                <button type="button" class="bottom-nav-item" onclick="toggleMobileDrawer(true)">
                    <i class="fa-solid fa-user-circle"></i>
                    <span>Menu</span>
                </button>
            @else
                <a href="{{ route('admin.dashboard') }}"
                    class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Admin</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="bottom-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Players</span>
                </a>
                <a href="{{ route('admin.withdrawals') }}"
                    class="bottom-nav-item {{ request()->routeIs('admin.withdrawals') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Payouts</span>
                </a>
                <a href="{{ route('admin.questions') }}"
                    class="bottom-nav-item {{ request()->routeIs('admin.questions') ? 'active' : '' }}">
                    <i class="fa-solid fa-database"></i>
                    <span>Questions</span>
                </a>
                <button type="button" class="bottom-nav-item" onclick="toggleMobileDrawer(true)">
                    <i class="fa-solid fa-ellipsis"></i>
                    <span>More</span>
                </button>
            @endif
        </nav>
    @endauth

    <!-- Global Top-Up Modal -->
    @auth
        <div id="topUpModal" class="modal-overlay">
            <div class="modal-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <div
                            style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                            <i class="fa-solid fa-coins fa-lg"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">Top-Up Quiwin Points</h3>
                    </div>
                    <button onclick="closeTopUpModal()"
                        style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; padding: 0.25rem;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.25rem;">
                    Select a top-up package or enter a custom amount of points to add to your balance.
                </p>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 1.25rem;">
                    <button type="button" class="btn btn-outline"
                        style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.9rem; font-weight: 700; color: #fcd34d; padding: 0.5rem;"
                        onclick="setTopUpAmount(100)">
                        +100 PTS
                    </button>
                    <button type="button" class="btn btn-outline"
                        style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.9rem; font-weight: 700; color: #fcd34d; padding: 0.5rem;"
                        onclick="setTopUpAmount(250)">
                        +250 PTS
                    </button>
                    <button type="button" class="btn btn-outline"
                        style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.9rem; font-weight: 700; color: #fcd34d; padding: 0.5rem;"
                        onclick="setTopUpAmount(500)">
                        +500 PTS
                    </button>
                </div>

                <form action="{{ route('user.topup') }}" method="POST" id="topUpForm">
                    @csrf
                    <div style="margin-bottom: 1.25rem;">
                        <label
                            style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                            Amount to Add (PTS)
                        </label>
                        <input type="number" name="amount" id="topUpAmountInput" min="10" max="10000"
                            value="100" class="form-input" required>
                    </div>

                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" class="btn btn-outline" style="flex: 1;"
                            onclick="closeTopUpModal()">Cancel</button>
                        <button type="submit" class="btn btn-gold" style="flex: 2;">
                            <i class="fa-solid fa-bolt"></i> Confirm Top-Up
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Global GCash Withdrawal Modal -->
        @if (!auth()->user()->isAdmin())
            <div id="withdrawModal" class="modal-overlay">
                <div class="modal-card" style="max-width: 500px;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div
                                style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.2rem;">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff;">GCash Withdrawal</h3>
                                <div style="font-size: 0.75rem; color: #94a3b8;">1 Point = ₱1.00 PHP (GCash Direct)</div>
                            </div>
                        </div>
                        <button onclick="closeWithdrawModal()"
                            style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; padding: 0.25rem;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Balance indicator card -->
                    <div
                        style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.85rem; padding: 0.85rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Available Balance</div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: #fbbf24;">
                                {{ number_format(auth()->user()->points) }} <span
                                    style="font-size: 0.8rem; color: #94a3b8;">PTS</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; color: #94a3b8;">Min. Payout</div>
                            <div style="font-size: 1rem; font-weight: 800; color: #34d399;">₱500.00</div>
                        </div>
                    </div>

                    @if (auth()->user()->points < 500)
                        <!-- Warning when balance is below 500 (e.g. 499) -->
                        <div
                            style="background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.35); border-radius: 0.85rem; padding: 1rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                            <i class="fa-solid fa-triangle-exclamation text-rose-400"
                                style="font-size: 1.25rem; margin-top: 0.15rem;"></i>
                            <div>
                                <div style="font-weight: 700; color: #fb7185; font-size: 0.92rem;">Cannot Withdraw Yet
                                </div>
                                <div style="font-size: 0.82rem; color: #cbd5e1; margin-top: 0.25rem; line-height: 1.4;">
                                    Minimum withdrawal is <strong>500 PTS (₱500)</strong>. Your balance is
                                    <strong>{{ number_format(auth()->user()->points) }} PTS</strong> (Need
                                    {{ 500 - auth()->user()->points }} more points). Play matches or invite friends to
                                    reach 500+ PTS!
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" class="btn btn-outline" style="flex: 1;"
                                onclick="closeWithdrawModal()">Close</button>
                            <button type="button" class="btn btn-gold" style="flex: 2;"
                                onclick="closeWithdrawModal(); openTopUpModal();">
                                <i class="fa-solid fa-coins"></i> Top-Up Points
                            </button>
                        </div>
                    @else
                        <!-- Active Withdrawal Form -->
                        <form action="{{ route('user.withdraw') }}" method="POST" id="withdrawForm"
                            onsubmit="return validateWithdrawForm(event)">
                            @csrf

                            <!-- Quick Amount Selection Buttons -->
                            <div style="margin-bottom: 0.85rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem; text-transform: uppercase;">
                                    Quick Amount
                                </label>
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem;">
                                    <button type="button" class="btn btn-outline"
                                        style="padding: 0.4rem 0.25rem; font-size: 0.8rem; font-weight: 700; color: #34d399; border-color: rgba(16, 185, 129, 0.3);"
                                        onclick="setWithdrawAmount(500)">
                                        ₱500
                                    </button>
                                    @if (auth()->user()->points >= 1000)
                                        <button type="button" class="btn btn-outline"
                                            style="padding: 0.4rem 0.25rem; font-size: 0.8rem; font-weight: 700; color: #34d399; border-color: rgba(16, 185, 129, 0.3);"
                                            onclick="setWithdrawAmount(1000)">
                                            ₱1,000
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline"
                                            style="padding: 0.4rem 0.25rem; font-size: 0.8rem; color: #64748b; opacity: 0.5;"
                                            disabled>
                                            ₱1,000
                                        </button>
                                    @endif

                                    @if (auth()->user()->points >= 2000)
                                        <button type="button" class="btn btn-outline"
                                            style="padding: 0.4rem 0.25rem; font-size: 0.8rem; font-weight: 700; color: #34d399; border-color: rgba(16, 185, 129, 0.3);"
                                            onclick="setWithdrawAmount(2000)">
                                            ₱2,000
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline"
                                            style="padding: 0.4rem 0.25rem; font-size: 0.8rem; color: #64748b; opacity: 0.5;"
                                            disabled>
                                            ₱2,000
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-outline"
                                        style="padding: 0.4rem 0.25rem; font-size: 0.8rem; font-weight: 800; color: #fbbf24; border-color: rgba(245, 158, 11, 0.4);"
                                        onclick="setWithdrawAmount({{ auth()->user()->points }})">
                                        MAX
                                    </button>
                                </div>
                            </div>

                            <!-- Amount input -->
                            <div style="margin-bottom: 0.85rem;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                                    <label style="font-size: 0.82rem; font-weight: 600; color: #cbd5e1;">
                                        Withdrawal Amount (₱ / PTS)
                                    </label>
                                    <span style="font-size: 0.72rem; color: #94a3b8;">Max:
                                        {{ number_format(auth()->user()->points) }} PTS</span>
                                </div>
                                <div style="position: relative;">
                                    <span
                                        style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #34d399; font-weight: 800; font-size: 1.1rem;">₱</span>
                                    <input type="number" name="amount" id="withdrawAmountInput" min="500"
                                        max="{{ auth()->user()->points }}" value="500" class="form-input"
                                        style="padding-left: 2rem; font-size: 1.05rem; font-weight: 700;" required
                                        oninput="validateAmountRealtime(this, {{ auth()->user()->points }})">
                                </div>
                                <div id="amountFeedbackText"
                                    style="font-size: 0.72rem; color: #34d399; margin-top: 0.25rem;">
                                    ✓ Valid withdrawal amount: ₱500 PHP
                                </div>
                            </div>

                            <!-- GCash Number -->
                            <div style="margin-bottom: 0.85rem;">
                                <label
                                    style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.3rem;">
                                    GCash Mobile Number (starts with 09)
                                </label>
                                <div style="position: relative;">
                                    <div
                                        style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #06b6d4; font-size: 1rem;">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                    </div>
                                    <input type="text" name="gcash_number" id="gcashNumberInput"
                                        placeholder="09123456789" maxlength="11" class="form-input"
                                        style="padding-left: 2.4rem; letter-spacing: 1px; font-family: monospace; font-size: 1rem;"
                                        required oninput="formatGCashNumber(this)">
                                </div>
                            </div>

                            <!-- GCash Account Name -->
                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.3rem;">
                                    GCash Registered Account Name
                                </label>
                                <div style="position: relative;">
                                    <div
                                        style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #a5b4fc; font-size: 1rem;">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <input type="text" name="gcash_name" id="gcashNameInput"
                                        placeholder="e.g. Juan Dela Cruz" class="form-input"
                                        style="padding-left: 2.4rem;" required>
                                </div>
                            </div>

                            <!-- Payout note -->
                            <div
                                style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 0.75rem; padding: 0.65rem 0.8rem; margin-bottom: 1rem; font-size: 0.75rem; color: #c7d2fe; line-height: 1.35;">
                                <i class="fa-solid fa-circle-info text-indigo-400"></i> Points are deducted when Admin
                                approves payout. You'll receive a notification <em>"Already sent by the admin"</em>.
                            </div>

                            <div style="display: flex; gap: 0.75rem;">
                                <button type="button" class="btn btn-outline" style="flex: 1;"
                                    onclick="closeWithdrawModal()">Cancel</button>
                                <button type="submit" id="submitWithdrawBtn" class="btn btn-success"
                                    style="flex: 2; padding: 0.75rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);">
                                    <i class="fa-solid fa-paper-plane"></i> Submit Payout
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Global In-Game Mailbox / Notifications Modal -->
            <div id="mailModal" class="modal-overlay">
                <div class="modal-card"
                    style="max-width: 580px; max-height: 85vh; max-height: 85dvh; display: flex; flex-direction: column;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.55rem;">
                            <div
                                style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1rem;">
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">In-Game Mailbox</h3>
                                <div style="font-size: 0.72rem; color: #94a3b8;">Official alerts & Admin payout
                                    notifications</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            @if (auth()->user()->unreadMails()->count() > 0)
                                <form action="{{ route('user.mail.readall') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline"
                                        style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #94a3b8; min-height: 30px;">
                                        <i class="fa-solid fa-check-double"></i> Read all
                                    </button>
                                </form>
                            @endif
                            <button onclick="closeMailModal()"
                                style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; padding: 0.2rem 0.35rem;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    @php
                        $userMailsList = auth()->user()->mails()->take(20)->get();
                    @endphp

                    <div
                        style="overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 0.65rem; padding-right: 0.2rem;">
                        @forelse($userMailsList as $mail)
                            <div
                                style="background: {{ $mail->is_read ? 'rgba(15, 23, 42, 0.6)' : 'rgba(30, 27, 75, 0.5)' }}; border: 1px solid {{ $mail->type === 'withdrawal_approved' ? 'rgba(16, 185, 129, 0.4)' : ($mail->is_read ? 'rgba(255, 255, 255, 0.06)' : 'rgba(99, 102, 241, 0.35)') }}; border-radius: 0.85rem; padding: 0.85rem; position: relative;">

                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.3rem;">
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        @if ($mail->type === 'withdrawal_approved')
                                            <span
                                                style="font-size: 0.68rem; font-weight: 800; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.12rem 0.45rem; border-radius: 9999px;">
                                                <i class="fa-solid fa-circle-check"></i> Already sent by admin
                                            </span>
                                        @elseif($mail->type === 'withdrawal_rejected')
                                            <span
                                                style="font-size: 0.68rem; font-weight: 800; background: rgba(244, 63, 94, 0.2); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.12rem 0.45rem; border-radius: 9999px;">
                                                <i class="fa-solid fa-ban"></i> Payout Rejected
                                            </span>
                                        @else
                                            <span
                                                style="font-size: 0.68rem; font-weight: 700; background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.12rem 0.45rem; border-radius: 9999px;">
                                                <i class="fa-solid fa-bell"></i> System Alert
                                            </span>
                                        @endif

                                        @if (!$mail->is_read)
                                            <span
                                                style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                        @endif
                                    </div>

                                    <span style="font-size: 0.7rem; color: #64748b;">
                                        {{ $mail->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <div style="font-size: 0.9rem; font-weight: 800; color: #fff; margin-bottom: 0.25rem;">
                                    {{ $mail->title }}
                                </div>

                                <p style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.4; margin-bottom: 0.4rem;">
                                    {{ $mail->message }}
                                </p>

                                @if (!$mail->is_read)
                                    <form action="{{ route('user.mail.read', $mail->id) }}" method="POST"
                                        style="margin: 0; text-align: right;">
                                        @csrf
                                        <button type="submit"
                                            style="background: none; border: none; font-size: 0.72rem; color: #818cf8; cursor: pointer; text-decoration: underline; padding: 0.2rem 0;">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div style="text-align: center; padding: 2rem 1rem; color: #64748b;">
                                <i class="fa-regular fa-envelope-open"
                                    style="font-size: 2.2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                <p style="font-size: 0.85rem;">Your mailbox is empty. System announcements and withdrawal
                                    receipts will appear here.</p>
                            </div>
                        @endforelse
                    </div>

                    <div
                        style="margin-top: 0.85rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.65rem; text-align: right;">
                        <button type="button" class="btn btn-outline"
                            style="font-size: 0.85rem; padding: 0.4rem 0.9rem; min-height: 36px;"
                            onclick="closeMailModal()">Close</button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Footer -->
    <footer
        style="border-top: 1px solid var(--bg-card-border); padding: 1.25rem 0; text-align: center; color: var(--text-muted); font-size: 0.8rem; margin-top: auto;">
        <div class="main-container">
            <p>Quiwin &bull; {{ date('Y') }}</p>
        </div>
    </footer>

    <!-- Audio FX Engine & Ambient Particles Script -->
    <script>
        // Web Audio Synthesizer for high-fidelity interactive game sounds
        class SoundFX {
            constructor() {
                this.ctx = null;
            }
            init() {
                if (!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    this.ctx = new AudioContext();
                }
            }
            playBeep(freq = 440, type = 'sine', duration = 0.1, gainVal = 0.15) {
                try {
                    this.init();
                    if (this.ctx.state === 'suspended') this.ctx.resume();
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
                    gain.gain.setValueAtTime(gainVal, this.ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + duration);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start();
                    osc.stop(this.ctx.currentTime + duration);
                } catch (e) {}
            }
            correct() {
                this.playBeep(523.25, 'sine', 0.12, 0.2); // C5
                setTimeout(() => this.playBeep(659.25, 'sine', 0.12, 0.2), 100); // E5
                setTimeout(() => this.playBeep(783.99, 'sine', 0.25, 0.25), 200); // G5
            }
            wrong() {
                this.playBeep(220, 'sawtooth', 0.2, 0.2);
                setTimeout(() => this.playBeep(180, 'sawtooth', 0.35, 0.25), 150);
            }
            streakLost() {
                this.playBeep(329.63, 'sawtooth', 0.12, 0.2);
                setTimeout(() => this.playBeep(261.63, 'sawtooth', 0.15, 0.22), 90);
                setTimeout(() => this.playBeep(196.00, 'sawtooth', 0.3, 0.28), 180);
            }
            tick() {
                this.playBeep(880, 'triangle', 0.04, 0.08);
            }
            streak() {
                this.playBeep(440, 'triangle', 0.1, 0.2);
                setTimeout(() => this.playBeep(554.37, 'triangle', 0.1, 0.2), 80);
                setTimeout(() => this.playBeep(659.25, 'triangle', 0.15, 0.25), 160);
                setTimeout(() => this.playBeep(880, 'triangle', 0.3, 0.3), 240);
            }
            victory() {
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, i) => {
                    setTimeout(() => this.playBeep(freq, 'sine', 0.3, 0.25), i * 150);
                });
            }
        }
        window.soundFX = new SoundFX();

        // Ambient canvas particles
        (function() {
            const canvas = document.getElementById('ambient-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w, h, particles = [];

            function resize() {
                w = canvas.width = window.innerWidth;
                h = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            const pCount = window.innerWidth < 768 ? 20 : 40;
            for (let i = 0; i < pCount; i++) {
                particles.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: Math.random() * 2 + 1,
                    dx: (Math.random() - 0.5) * 0.3,
                    dy: (Math.random() - 0.5) * 0.3,
                    alpha: Math.random() * 0.35 + 0.1
                });
            }

            function animate() {
                ctx.clearRect(0, 0, w, h);
                particles.forEach(p => {
                    p.x += p.dx;
                    p.y += p.dy;
                    if (p.x < 0) p.x = w;
                    if (p.x > w) p.x = 0;
                    if (p.y < 0) p.y = h;
                    if (p.y > h) p.y = 0;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(165, 180, 252, ${p.alpha})`;
                    ctx.fill();
                });
                requestAnimationFrame(animate);
            }
            animate();
        })();

        // Mobile Drawer Toggle
        function toggleMobileDrawer(open) {
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('mobileDrawerOverlay');
            if (!drawer || !overlay) return;

            if (open === undefined) {
                const isActive = drawer.classList.contains('active');
                drawer.classList.toggle('active', !isActive);
                overlay.classList.toggle('active', !isActive);
            } else if (open) {
                drawer.classList.add('active');
                overlay.classList.add('active');
            } else {
                drawer.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        // Modal Helpers
        function openTopUpModal() {
            const modal = document.getElementById('topUpModal');
            if (modal) modal.classList.add('active');
        }

        function closeTopUpModal() {
            const modal = document.getElementById('topUpModal');
            if (modal) modal.classList.remove('active');
        }

        function setTopUpAmount(amt) {
            const input = document.getElementById('topUpAmountInput');
            if (input) input.value = amt;
        }

        // Withdrawal Modal Helpers
        function openWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            if (modal) modal.classList.add('active');
        }

        function closeWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            if (modal) modal.classList.remove('active');
        }

        function setWithdrawAmount(amt) {
            const input = document.getElementById('withdrawAmountInput');
            if (input) {
                input.value = amt;
                input.dispatchEvent(new Event('input'));
            }
        }

        function validateAmountRealtime(input, maxBalance) {
            const val = parseInt(input.value, 10);
            const feedback = document.getElementById('amountFeedbackText');
            const submitBtn = document.getElementById('submitWithdrawBtn');

            if (isNaN(val) || val < 500) {
                if (feedback) {
                    feedback.style.color = '#fb7185';
                    feedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Minimum withdrawal is 500 PTS (₱500).';
                }
                if (submitBtn) submitBtn.disabled = true;
            } else if (val > maxBalance) {
                if (feedback) {
                    feedback.style.color = '#fb7185';
                    feedback.innerHTML =
                        `<i class="fa-solid fa-circle-xmark"></i> Amount cannot exceed balance (${maxBalance.toLocaleString()} PTS).`;
                }
                if (submitBtn) submitBtn.disabled = true;
            } else {
                if (feedback) {
                    feedback.style.color = '#34d399';
                    feedback.innerHTML =
                        `✓ Valid withdrawal amount: ₱${val.toLocaleString()} PHP (${val.toLocaleString()} PTS)`;
                }
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        function formatGCashNumber(input) {
            let val = input.value.replace(/\D/g, '');
            if (val.length > 11) val = val.substring(0, 11);
            input.value = val;
        }

        function validateWithdrawForm(e) {
            const gcashInput = document.getElementById('gcashNumberInput');
            const nameInput = document.getElementById('gcashNameInput');
            const amountInput = document.getElementById('withdrawAmountInput');

            if (gcashInput) {
                const gcash = gcashInput.value.trim();
                if (!/^09\d{9}$/.test(gcash)) {
                    alert('Invalid GCash Number: Must start with 09 and contain exactly 11 digits (e.g. 09123456789).');
                    gcashInput.focus();
                    e.preventDefault();
                    return false;
                }
            }

            if (nameInput && nameInput.value.trim().length < 2) {
                alert('Please enter a valid GCash account name.');
                nameInput.focus();
                e.preventDefault();
                return false;
            }

            if (amountInput) {
                const amt = parseInt(amountInput.value, 10);
                if (isNaN(amt) || amt < 500) {
                    alert('Minimum withdrawal amount is 500 PTS (₱500).');
                    amountInput.focus();
                    e.preventDefault();
                    return false;
                }
            }

            return true;
        }

        // Mailbox Modal Helpers
        function openMailModal() {
            const modal = document.getElementById('mailModal');
            if (modal) modal.classList.add('active');
        }

        function closeMailModal() {
            const modal = document.getElementById('mailModal');
            if (modal) modal.classList.remove('active');
        }

        // Global Click Outside & Escape Key Modal Handlers
        document.addEventListener('DOMContentLoaded', () => {
            // Close modal when clicking on dark backdrop overlay
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        if (overlay.id === 'roundBreakModal') {
                            if (typeof proceedToNextRound === 'function') {
                                proceedToNextRound();
                            }
                            return;
                        }
                        if (overlay.id === 'gameLoadingModal' || overlay.id === 'antiCheatModal' ||
                            overlay.id === 'bankruptcyModal') {
                            return; // Protected game state modals
                        }
                        overlay.classList.remove('active');
                    }
                });
            });

            // Close on Escape key press
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                        if (modal.id === 'roundBreakModal') {
                            if (typeof proceedToNextRound === 'function') {
                                proceedToNextRound();
                            }
                        } else if (modal.id !== 'gameLoadingModal' && modal.id !==
                            'antiCheatModal' && modal.id !== 'bankruptcyModal') {
                            modal.classList.remove('active');
                        }
                    });
                    toggleMobileDrawer(false);
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
