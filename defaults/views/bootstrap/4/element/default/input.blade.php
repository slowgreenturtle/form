<div class="{{ $element->getClass('div', true) }}" id="{{ $element->getDivID() }}">

    {!! $element->drawLabel() !!}

    @if($element->getData('prepend') || $element->getData('append'))
        <div class="form-group">
            @endif

            @if($element->getData('prepend'))
                <span class="input-group-prepend">{!! $element->getData('prepend') !!}</span>
            @endif

            {!! $element->drawElement() !!}

            @if($element->getData('append'))
                <span class="input-group-append">{!! $element->getData('append') !!}</span>
            @endif

            @if($element->getData('prepend') || $element->getData('append'))
        </div>
    @endif

    @if(!empty($element->getData('help')))
        <small>{!! $element->getData('help') !!}</small>
    @endif
</div>
