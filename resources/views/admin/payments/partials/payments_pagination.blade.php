<ul class="pagination pagination-sm mb-0">
    @if ($payments->onFirstPage())
        <li class="page-item disabled"><span class="page-link rounded-start-3">&laquo;</span></li>
    @else
        <li class="page-item">
            <a class="page-link rounded-start-3" href="{{ $payments->previousPageUrl() }}">&laquo;</a>
        </li>
    @endif

    @foreach(range(1, $payments->lastPage()) as $i)
        <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
            <a class="page-link" href="{{ $payments->appends(request()->query())->url($i) }}">{{ $i }}</a>
        </li>
    @endforeach

    @if ($payments->hasMorePages())
        <li class="page-item">
            <a class="page-link rounded-end-3" href="{{ $payments->nextPageUrl() }}">&raquo;</a>
        </li>
    @else
        <li class="page-item disabled"><span class="page-link rounded-end-3">&raquo;</span></li>
    @endif
</ul>