<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class SelectTag extends Select
{

    protected $type      = 'select_tag';
    protected $type_file = 'select_tag';

    public function __construct()
    {

        parent::__construct();

        $this->multiple(true)->options([])->value('');
        $this->addClass('element', 'select2-multiple');

        $this->attribute('aria-hidden', true);

    }
}
