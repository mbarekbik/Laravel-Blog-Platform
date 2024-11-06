@if ($paginator->lastPage() > 1)
<ul class="pagination">
    <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
        <a href="{{ $paginator->url($paginator->currentPage() - 1) }}">Poprzednia</a>
    </li>
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
            <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
        </li>
    @endfor
    <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
        <a href="{{ ($paginator->currentPage() != $paginator->lastPage()) ? $paginator->url($paginator->currentPage()+1) : $paginator->url($paginator->currentPage()) }}" >Następna</a>
    </li>
</ul>
@endif
