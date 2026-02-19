@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-2 py-8">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-link disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev" aria-label="@lang('pagination.previous')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden md:flex items-center space-x-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="pagination-ellipsis" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-link active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="flex md:hidden items-center px-4 text-center">
            <span class="text-xs font-bold pagination-mobile-indicator">
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            </span>
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next" aria-label="@lang('pagination.next')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @else
            <span class="pagination-link disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @endif
    </nav>

    <style>
        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--card-border, rgba(0,0,0,0.05));
            color: var(--text-dim, #94a3b8);
            font-weight: 800;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            -webkit-tap-highlight-color: transparent;
        }

        .pagination-link:hover:not(.disabled):not(.active) {
            transform: translateY(-3px);
            background: var(--hover-bg, rgba(0,0,0,0.02));
            color: var(--brand-blue, #3b82f6);
            border-color: var(--brand-blue, #3b82f6);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
        }

        .pagination-link.active {
            background: var(--brand-blue, #3b82f6);
            color: white !important;
            border-color: var(--brand-blue, #3b82f6);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
            transform: scale(1.05);
        }

        .pagination-link.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: transparent;
        }

        .pagination-ellipsis {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            color: var(--text-dim, #94a3b8);
            font-weight: 900;
            letter-spacing: 2px;
        }

        .dark .pagination-link {
            background: #1f2937;
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            color: var(--text-secondary, #94a3b8);
        }
        
        .dark .pagination-ellipsis {
            color: var(--text-secondary, #94a3b8);
        }
        
        .dark .pagination-link:hover:not(.disabled):not(.active) {
            background: rgba(255,255,255,0.05);
            color: var(--brand-blue, #3b82f6);
        }

        .pagination-mobile-indicator {
            color: var(--text-main, #1e293b);
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .pagination-link {
                width: 40px;
                height: 40px;
                border-radius: 12px;
            }
        }
    </style>
@endif
