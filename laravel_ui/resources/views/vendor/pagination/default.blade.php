@if ($paginator->hasPages())
<nav style="display:inline-flex;align-items:center;gap:.3rem;font-family:inherit;font-size:.85rem;">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:.35rem .65rem;border:1px solid #dde8e8;border-radius:7px;color:#b0c4c4;cursor:default;user-select:none;">&laquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:.35rem .65rem;border:1px solid #dde8e8;border-radius:7px;color:var(--teal,#0c7070);text-decoration:none;transition:background .15s;" onmouseover="this.style.background='var(--teal-light,#e6f4f4)'" onmouseout="this.style.background=''">&laquo;</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:.35rem .5rem;color:#9bb0b0;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:.35rem .65rem;border:1px solid var(--teal,#0c7070);border-radius:7px;background:var(--teal,#0c7070);color:#fff;font-weight:600;cursor:default;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:.35rem .65rem;border:1px solid #dde8e8;border-radius:7px;color:var(--teal,#0c7070);text-decoration:none;transition:background .15s;" onmouseover="this.style.background='var(--teal-light,#e6f4f4)'" onmouseout="this.style.background=''">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:.35rem .65rem;border:1px solid #dde8e8;border-radius:7px;color:var(--teal,#0c7070);text-decoration:none;transition:background .15s;" onmouseover="this.style.background='var(--teal-light,#e6f4f4)'" onmouseout="this.style.background=''">&raquo;</a>
    @else
        <span style="padding:.35rem .65rem;border:1px solid #dde8e8;border-radius:7px;color:#b0c4c4;cursor:default;user-select:none;">&raquo;</span>
    @endif

</nav>
@endif
