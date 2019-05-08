<div class="btn-group" role="group">
    @foreach($items as $item)
        {!! $item->display() !!}
    @endforeach
</div>
