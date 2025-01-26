<div class="btn-group">

    <a class="{{ implode(' ', $dropdown->classes()) }}"
       href="{{ $dropdown->gethRef() }}">{{ $dropdown->getLabel() }}</a>

    @if(count($items))
        <button type="button"
                class="btn dropdown-toggle dropdown-toggle-split {{ $dropdown->getColorClass() }} {{ $dropdown->getSizeClass() }}"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>

        <div class="{{ $dropdown->dropdownMenuClasses() }}">
            @foreach($items as $counter=>$item)

                @if($item->type == 'divider')
                    <div class="dropdown-divider"></div>
                @else
                    {!! $item->display() !!}
                @endif
            @endforeach
        </div>
    @endif
</div>

@if(count($items) > 0)

    <div class="btn-group">
        {!! $button->display() !!}

        @if(count($items))
            <button type="button"
                    class="btn {{ $button->getColorClass() }} {{ $button->getSizeClass() }} dropdown-toggle"
                    data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="{!! $dropdown->dropdownMenuClasses() !!}" role="menu">

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
@else
    {!! $button->display() !!}
@endif
