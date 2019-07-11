<div class="{{ $div_classes }}" id="{{ $div_name}}">
    {!! $label !!}
    @if($prepend_text != null || $append_text != null)
        <div class="input-group">
            @endif

            @if($prepend_text != null)
                <div class="input-group-prepend">
                    <span class="input-group-text">{{ $prepend_text }}</span>
                </div>
            @endif

            {{ $form_element }}

            @if($append_text != null)
                <div class="input-group-append">
                    <span class="input-group-text">{{ $append_text }}</span>
                </div>
            @endif

            @if($prepend_text != null || $append_text != null)
        </div>
    @endif

    @if(!empty($help))

        <small>{{ $help }}</small>
    @endif
</div>
