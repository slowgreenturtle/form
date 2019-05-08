<div class="btn-group">

    <a type="button" class="btn {{ implode(' ', $dropdown->classes()) }}"
       href="{{ $dropdown->gethRef() }}">{{ $dropdown->getLabel() }}</a>

    @if(count($items))
        <button type="button"
                class="btn {{ $dropdown->getColorClass() }} {{ $dropdown->getSizeClass() }} dropdown-toggle"
                data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">

            @foreach($items as $counter=>$item)

                @if($item->type == 'divider')
                    <li class="divider"></li>
                @else
                    <li>{!! $item->display() !!}</li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
