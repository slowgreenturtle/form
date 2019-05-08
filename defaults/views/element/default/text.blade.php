<div class="{{ $div_classes }}" id="{{ $div_name}}">
    {!! $label !!}
    @if($prepend_text != null || $append_text != null)
        <div class="input-group">
            @endif

            @if($prepend_text != null)
                <span class="input-group-addon input-group-prepend">{{$prepend_text}}</span>
            @endif

            {{ $form_element }}

            @if($append_text != null)
                <span class="input-group-addon input-group-append">{{  $append_text }}</span>
            @endif

            @if($prepend_text != null || $append_text != null)
        </div>
    @endif

    @if(!empty($help))

        <small>{{ $help }}</small>
    @endif
</div>
