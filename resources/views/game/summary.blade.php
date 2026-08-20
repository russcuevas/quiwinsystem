@extends('layouts.app')

@section('title', 'Match Summary - Quiwin')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; position: relative;">

        <!-- Canvas for Victory Confetti -->
        <canvas id="confetti-canvas"
            style="position: fixed; inset: 0; pointer-events: none; z-index: 99; width: 100%; height: 100%;"></canvas>

        <!-- Header / Banner -->
        <div class="glass-card"
            style="padding: 2.5rem; text-align: center; margin-bottom: 2rem; border: 1px solid rgba(99, 102, 241, 0.35); position: relative; overflow: hidden;">

            <div
                style="width: 72px; height: 72px; margin: 0 auto 1.25rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #090d16; font-size: 2.5rem; box-shadow: 0 0 32px rgba(245, 158, 11, 0.5);">
                <i class="fa-solid fa-trophy"></i>
            </div>

            <div
                style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.85rem; color: #a5b4fc; font-weight: 600; margin-bottom: 0.75rem;">
                Match #{{ $session->id }} Completed
            </div>

            <h1 style="font-size: 2.5rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.5px;">
                @if ($session->total_correct >= 24)
                    Legendary Performance! 👑
                @elseif($session->total_correct >= 18)
                    Fantastic Game! ⚡
                @elseif($session->total_correct >= 12)
                    Good Effort! 🛡️
                @else
                    Keep Training! 🎯
                @endif
            </h1>
            <p style="color: #cbd5e1; font-size: 1.05rem;">
                You completed all 30 questions across Easy, Normal, and Hard stages.
            </p>

            <!-- 4 Summary Stat Boxes -->
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 2rem;">

                <div
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; padding: 1.25rem;">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Score</div>
                    <div style="font-size: 2rem; font-weight: 900; color: #34d399; margin-top: 0.25rem;">
                        {{ $session->total_correct }} <span style="font-size: 1.1rem; color: #64748b;">/ 30</span>
                    </div>
                </div>

                <div
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; padding: 1.25rem;">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Max Streak
                    </div>
                    <div style="font-size: 2rem; font-weight: 900; color: #fbbf24; margin-top: 0.25rem;">
                        {{ $session->max_streak }} 🔥
                    </div>
                </div>

                <div
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; padding: 1.25rem;">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Net Points
                        Delta</div>
                    <div
                        style="font-size: 2rem; font-weight: 900; color: {{ $session->points_delta >= 0 ? '#34d399' : '#fb7185' }}; margin-top: 0.25rem;">
                        {{ $session->points_delta >= 0 ? '+' . $session->points_delta : $session->points_delta }} PTS
                    </div>
                </div>

                <div
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; padding: 1.25rem;">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Current
                        Balance</div>
                    <div style="font-size: 2rem; font-weight: 900; color: #a5b4fc; margin-top: 0.25rem;">
                        {{ number_format($user->points) }} PTS
                    </div>
                </div>

            </div>

            <!-- Action CTAs -->
            <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('user.home') }}" class="btn btn-outline"
                    style="padding: 0.85rem 1.5rem; font-size: 1rem;">
                    <i class="fa-solid fa-house"></i> Back to Hub
                </a>

                @if ($user->points >= 50)
                    <form action="{{ route('game.start') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-primary"
                            style="padding: 0.85rem 1.75rem; font-size: 1rem; background: linear-gradient(135deg, #6366f1, #06b6d4);">
                            <i class="fa-solid fa-rotate-right"></i> Play Again (-50 PTS)
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-gold" style="padding: 0.85rem 1.75rem; font-size: 1rem;"
                        onclick="openTopUpModal()">
                        <i class="fa-solid fa-plus-circle"></i> Top-Up to Play Again
                    </button>
                @endif
            </div>

        </div>



    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.soundFX.victory();
            launchConfetti();
        });

        function launchConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            const pieces = [];
            const colors = ['#6366f1', '#06b6d4', '#f59e0b', '#10b981', '#f43f5e', '#a855f7'];

            for (let i = 0; i < 100; i++) {
                pieces.push({
                    x: Math.random() * width,
                    y: Math.random() * height - height,
                    w: Math.random() * 8 + 4,
                    h: Math.random() * 8 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    dx: (Math.random() - 0.5) * 3,
                    dy: Math.random() * 3 + 2,
                    rotation: Math.random() * 360,
                    dr: (Math.random() - 0.5) * 5
                });
            }

            let frame = 0;

            function update() {
                ctx.clearRect(0, 0, width, height);
                pieces.forEach(p => {
                    p.x += p.dx;
                    p.y += p.dy;
                    p.rotation += p.dr;

                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rotation * Math.PI) / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });

                frame++;
                if (frame < 240) {
                    requestAnimationFrame(update);
                } else {
                    ctx.clearRect(0, 0, width, height);
                }
            }
            update();
        }
    </script>
@endpush
