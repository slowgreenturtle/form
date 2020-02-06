<div class="{{ $element->getClass('div', true) }}" id="{{ $element->getDivID() }}">

    {!! $element->drawLabel() !!}

    @if($element->getData('prepend') || $element->getData('append'))
        <div class="input-group">
            @endif

            @if($element->getData('prepend'))
                <span class="input-group-addon">{!! $element->getData('prepend') !!}</span>
            @endif

            {!! $element->drawElement !!}

            @if($element->getData('append'))
                <span class="input-group-addon">{!! $element->getData('append') !!}</span>
            @endif

            @if($element->getData('prepend') || $element->getData('append'))
        </div>
    @endif

    @if(!empty($element->getData('help')))
        <small>{!! $element->getData('help') !!}</small>
    @endif
</div>