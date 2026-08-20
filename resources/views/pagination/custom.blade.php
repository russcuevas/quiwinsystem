@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.9rem;">
        
        <!-- Showing Results Text -->
        <div style="color: #94a3b8; font-size: 0.85rem;">
            Showing 
            <span style="font-weight: 700; color: #fff;">{{ $paginator->firstItem() ?? 0 }}</span> 
            to 
            <span style="font-weight: 700; color: #fff;">{{ $paginator->lastItem() ?? 0 }}</span> 
            of 
            <span style="font-weight: 700; color: #fff;">{{ $paginator->total() }}</span> 
            results
        </div>

        <!-- Page Numbers List -->
        <div style="display: flex; align-items: center; gap: 0.35rem;">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 0.45rem 0.85rem; border-radius: 0.6rem; background: rgba(255,255,255,0.03); color: #475569; border: 1px solid rgba(255,255,255,0.05); cursor: not-allowed;">
                    <i class="fa-solid fa-chevron-left"></i> Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 0.6rem;">
                    <i class="fa-solid fa-chevron-left"></i> Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 0.45rem 0.75rem; color: #64748b;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 0.45rem 0.85rem; border-radius: 0.6rem; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; font-weight: 700; box-shadow: 0 0 12px rgba(99, 102, 241, 0.4); border: 1px solid #6366f1;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 0.6rem;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 0.6rem;">
                    Next <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span style="padding: 0.45rem 0.85rem; border-radius: 0.6rem; background: rgba(255,255,255,0.03); color: #475569; border: 1px solid rgba(255,255,255,0.05); cursor: not-allowed;">
                    Next <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif

        </div>

    </nav>
@endif
