<div class="d-flex justify-content-between align-items-center" style="padding: 20px 0;">
    <div class="text-muted" style="font-size: 14px;">
        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}
        entries
    </div>
    <div>
        @if ($payments->hasPages())
            <nav aria-label="Payments pagination">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($payments->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="border-radius: 6px 0 0 6px;">
                                <i class="fa fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->previousPageUrl() }}" rel="prev"
                                style="border-radius: 6px 0 0 6px;">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($payments->links()->elements[0] as $page => $url)
                        @if ($page == $payments->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link" style="background: #643271; border-color: #643271;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($payments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->nextPageUrl() }}" rel="next"
                                style="border-radius: 0 6px 6px 0;">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="border-radius: 0 6px 6px 0;">
                                <i class="fa fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>

<style>
    .pagination .page-link {
        color: #643271;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 6px;
    }

    .pagination .page-link:hover {
        background: #643271;
        color: white;
        border-color: #643271;
    }

    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #643271;
        border-color: #643271;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>
