@if ($paginator->hasPages())
    <div style="display: flex; justify-content: center; align-items: center; margin: 20px 0; gap: 10px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 8px 12px; border: 1px solid #ddd; background: #f9f9f9; color: #999;">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid #ddd; background: #fff; color: #333; text-decoration: none;">Previous</a>
        @endif

        {{-- Page Info --}}
        <span style="padding: 8px 12px; color: #666;">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }} ({{ $paginator->total() }} total)</span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid #ddd; background: #fff; color: #333; text-decoration: none;">Next</a>
        @else
            <span style="padding: 8px 12px; border: 1px solid #ddd; background: #f9f9f9; color: #999;">Next</span>
        @endif
    </div>
@endif