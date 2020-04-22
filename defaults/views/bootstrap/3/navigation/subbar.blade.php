<div class="btn-group" role="group">
    @foreach($items as $item)
        @if($item->canDisplay() == true)
            {!! $item->display() !!}
        @endif
    @endforeach
</div>
