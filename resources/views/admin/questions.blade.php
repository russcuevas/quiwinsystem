@extends('layouts.app')

@section('title', 'Question Bank - Admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 0.4rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="font-size: 2rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">OpenTDB Question Bank</h1>
        </div>

        <!-- Sync Actions -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <form action="{{ route('admin.questions.sync') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="difficulty" value="easy">
                <button type="submit" class="btn btn-outline" style="border-color: rgba(16, 185, 129, 0.4); color: #34d399; font-size: 0.85rem;">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Sync Easy (+15)
                </button>
            </form>
            <form action="{{ route('admin.questions.sync') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="difficulty" value="medium">
                <button type="submit" class="btn btn-outline" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 0.85rem;">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Sync Medium (+15)
                </button>
            </form>
            <form action="{{ route('admin.questions.sync') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="difficulty" value="hard">
                <button type="submit" class="btn btn-outline" style="border-color: rgba(244, 63, 94, 0.4); color: #fb7185; font-size: 0.85rem;">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Sync Hard (+15)
                </button>
            </form>
            <form action="{{ route('admin.questions.sync') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="difficulty" value="all">
                <button type="submit" class="btn btn-primary" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrows-rotate"></i> Sync All Difficulties
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card" style="padding: 1.25rem 1.5rem;">
        <form action="{{ route('admin.questions') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
            <div style="flex: 1; min-width: 240px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search question text..." class="form-input">
            </div>

            <div style="min-width: 150px;">
                <select name="difficulty" class="form-input">
                    <option value="">All Difficulties</option>
                    <option value="easy" {{ $difficulty === 'easy' ? 'selected' : '' }}>Easy (Round 1)</option>
                    <option value="medium" {{ $difficulty === 'medium' ? 'selected' : '' }}>Medium (Round 2)</option>
                    <option value="hard" {{ $difficulty === 'hard' ? 'selected' : '' }}>Hard (Round 3)</option>
                </select>
            </div>

            <div style="min-width: 180px;">
                <select name="category" class="form-input">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Apply Filters
            </button>

            @if($search || $difficulty || $category)
                <a href="{{ route('admin.questions') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Questions List -->
    <div class="glass-card" style="padding: 1.75rem;">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($questions as $q)
            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 1rem; padding: 1.25rem;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <span style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 800; text-transform: uppercase;
                            @if($q->difficulty === 'easy') background: rgba(16, 185, 129, 0.2); color: #34d399;
                            @elseif($q->difficulty === 'medium') background: rgba(245, 158, 11, 0.2); color: #fbbf24;
                            @else background: rgba(244, 63, 94, 0.2); color: #fb7185;
                            @endif">
                            {{ $q->difficulty }}
                        </span>

                        <span style="font-size: 0.8rem; color: #a5b4fc; background: rgba(99, 102, 241, 0.15); padding: 0.2rem 0.5rem; border-radius: 6px;">
                            {{ $q->category }}
                        </span>
                    </div>

                    <span style="font-size: 0.75rem; color: #64748b;">Hash: {{ substr($q->question_hash, 0, 10) }}...</span>
                </div>

                <div style="font-size: 1.05rem; font-weight: 600; color: #fff; margin-bottom: 0.75rem;">
                    {!! $q->question_text !!}
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; font-size: 0.85rem;">
                    <span style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.3rem 0.75rem; border-radius: 6px; font-weight: 600;">
                        <i class="fa-solid fa-check"></i> Correct: {{ $q->correct_answer }}
                    </span>

                    @foreach($q->incorrect_answers ?? [] as $inc)
                    <span style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); color: #94a3b8; padding: 0.3rem 0.75rem; border-radius: 6px;">
                        {{ $inc }}
                    </span>
                    @endforeach
                </div>

            </div>
            @empty
            <p style="color: #64748b; text-align: center; padding: 2rem;">No questions found. Click "Sync from OpenTDB" to populate the database.</p>
            @endforelse
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $questions->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
