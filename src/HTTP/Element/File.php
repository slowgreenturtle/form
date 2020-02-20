<?php

namespace SGT\HTTP\Element;

class File extends Input
{

    protected $type      = 'file';
    protected $type_file = 'file';

    public function supportImage(string $fileName, int $width, int $height, string $alt_text = '')
    {

        $this->data('support_image', $fileName);
        $this->data('support_image_width', $width);
        $this->data('support_image_height', $height);
        $this->data('support_image_alt_text', $alt_text);

        return $this;
    }

    public function drawElement()
    {

        $element_name = $this->getName();
        $type         = $this->getType();

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        return Form::input($type, $element_name, $this->getValue(), $attributes);
    }
}