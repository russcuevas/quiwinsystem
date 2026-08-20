<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quiwin') - Ultimate Quiz Challenge</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
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

        /* Glassmorphism Navigation */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 13, 22, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--bg-card-border);
            padding: 0.85rem 0;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        .brand-logo .logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 16px var(--primary-glow);
            color: #fff;
            font-size: 1.15rem;
        }

        .brand-logo span {
            background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Points Pill Badge */
        .points-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.25));
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 0.45rem 0.95rem;
            border-radius: 9999px;
            font-weight: 700;
            color: #fbbf24;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.15);
            transition: all 0.2s ease;
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
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
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
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            transform: scale(0.95);
            transition: transform 0.25s ease;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Audio FX Engine using Web Audio API */
        /* Sound effects synthesized natively with no external lag */
    </style>
    @stack('styles')
</head>
<body>
    <!-- Ambient Canvas for Background Particles -->
    <canvas id="ambient-canvas" class="ambient-glow"></canvas>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="main-container">
            <div class="nav-inner">
                <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.home')) : route('login') }}" class="brand-logo">
                    <div class="logo-icon">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <span>QUIWIN</span>
                </a>

                <div class="nav-actions">
                    @auth
                        @if(!auth()->user()->isAdmin())
                            <!-- Points balance -->
                            <div class="points-badge" id="nav-points-badge" title="Your current points balance">
                                <i class="fa-solid fa-coins"></i>
                                <span id="nav-user-points">{{ number_format(auth()->user()->points) }}</span> PTS
                            </div>

                            <!-- Quick Top Up Button -->
                            <button type="button" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;" onclick="openTopUpModal()">
                                <i class="fa-solid fa-plus-circle text-amber-400"></i> Top-Up
                            </button>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Admin Panel
                            </a>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-users text-cyan-400"></i> Players
                            </a>
                            <a href="{{ route('admin.questions') }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-database text-purple-400"></i> Questions
                            </a>
                            <a href="{{ route('admin.settings') }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-color: rgba(16, 185, 129, 0.4); color: #34d399;">
                                <i class="fa-solid fa-sliders text-emerald-400"></i> Pointing Rules
                            </a>
                        @endif

                        <!-- User Profile Info & Logout -->
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-weight: 600; color: #e2e8f0; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fa-solid fa-circle-user text-indigo-400" style="font-size: 1.1rem;"></i>
                                {{ auth()->user()->name }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding: 0.45rem 0.75rem; font-size: 0.85rem; color: #f87171;" title="Logout">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fa-solid fa-sparkles"></i> Register (+200 PTS)
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main style="flex: 1; padding: 2rem 0;">
        <div class="main-container">
            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert-box alert-info">
                    <i class="fa-solid fa-circle-info fa-lg"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Global Top-Up Modal -->
    @auth
    <div id="topUpModal" class="modal-overlay">
        <div class="modal-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                        <i class="fa-solid fa-coins fa-lg"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">Top-Up Quiwin Points</h3>
                </div>
                <button onclick="closeTopUpModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Select a top-up package or enter a custom amount of points to add to your balance.
            </p>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
                <button type="button" class="btn btn-outline" style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.95rem; font-weight: 700; color: #fcd34d;" onclick="setTopUpAmount(100)">
                    +100 PTS
                </button>
                <button type="button" class="btn btn-outline" style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.95rem; font-weight: 700; color: #fcd34d;" onclick="setTopUpAmount(250)">
                    +250 PTS
                </button>
                <button type="button" class="btn btn-outline" style="border-color: rgba(245, 158, 11, 0.4); font-size: 0.95rem; font-weight: 700; color: #fcd34d;" onclick="setTopUpAmount(500)">
                    +500 PTS
                </button>
            </div>

            <form action="{{ route('user.topup') }}" method="POST" id="topUpForm">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                        Amount to Add (PTS)
                    </label>
                    <input type="number" name="amount" id="topUpAmountInput" min="10" max="10000" value="100" class="form-input" required>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeTopUpModal()">Cancel</button>
                    <button type="submit" class="btn btn-gold" style="flex: 2;">
                        <i class="fa-solid fa-bolt"></i> Confirm Top-Up
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <!-- Footer -->
    <footer style="border-top: 1px solid var(--bg-card-border); padding: 1.5rem 0; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
        <div class="main-container">
            <p>Quiwin Interactive Quiz Platform &bull; Powered by Open Trivia DB &bull; {{ date('Y') }}</p>
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
                } catch(e){}
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
            if(!canvas) return;
            const ctx = canvas.getContext('2d');
            let w, h, particles = [];

            function resize() {
                w = canvas.width = window.innerWidth;
                h = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            for(let i = 0; i < 40; i++) {
                particles.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: Math.random() * 2 + 1,
                    dx: (Math.random() - 0.5) * 0.4,
                    dy: (Math.random() - 0.5) * 0.4,
                    alpha: Math.random() * 0.4 + 0.1
                });
            }

            function animate() {
                ctx.clearRect(0, 0, w, h);
                particles.forEach(p => {
                    p.x += p.dx;
                    p.y += p.dy;
                    if(p.x < 0) p.x = w;
                    if(p.x > w) p.x = 0;
                    if(p.y < 0) p.y = h;
                    if(p.y > h) p.y = 0;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(165, 180, 252, ${p.alpha})`;
                    ctx.fill();
                });
                requestAnimationFrame(animate);
            }
            animate();
        })();

        // Modal Helpers
        function openTopUpModal() {
            const modal = document.getElementById('topUpModal');
            if(modal) modal.classList.add('active');
        }
        function closeTopUpModal() {
            const modal = document.getElementById('topUpModal');
            if(modal) modal.classList.remove('active');
        }
        function setTopUpAmount(amt) {
            const input = document.getElementById('topUpAmountInput');
            if(input) input.value = amt;
        }
    </script>
    @stack('scripts')
</body>
</html>
