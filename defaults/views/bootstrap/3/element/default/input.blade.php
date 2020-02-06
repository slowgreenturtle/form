<div class="{{ $element->getClasses('div', true) }}" id="{{ $element->getDivID() }}">

    {!! $label !!}

    @if($prepend_text != null || $append_text != null)
        <div class="input-group">
            @endif

            @if($prepend_text != null)
                <span class="input-group-addon">{{$prepend_text}}</span>
            @endif

            {{ $form_element }}

            @if($append_text != null)
                <span class="input-group-addon">{{  $append_text }}</span>
            @endif

            @if($prepend_text != null || $append_text != null)
        </div>
    @endif

    @if(!empty($help))

        <small>{{ $help }}</small>
    @endif
</div>
