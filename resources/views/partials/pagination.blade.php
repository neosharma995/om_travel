@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <li><span class="pagination-btn is-disabled" aria-disabled="true">&lsaquo;</span></li>
            @else
                <li><a class="pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&lsaquo;</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-btn is-disabled">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="pagination-btn is-active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="pagination-btn" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&rsaquo;</a></li>
            @else
                <li><span class="pagination-btn is-disabled" aria-disabled="true">&rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
