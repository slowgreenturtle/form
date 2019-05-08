<?php

return [
    'element'    =>
        [
            'view'  =>
                [
                    'path' => 'sgtform::element.default'
                ],
            'input' =>
                [
                    'css' =>
                        [
                            'default' => 'form-control',
                            'error'   => 'form-control-danger'
                        ]
                ],
        ],
    'table'      => [
        'datatable' => [
            'default' => 'sgtform::table.datatable.default'
        ]
    ],
    'navigation' => [
        'subbar' => 'sgtform::navigation.subbar'
    ],
    'colors'     => [
        'green'      => 'btn-success',
        'blue'       => 'btn-primary',
        'red'        => 'btn-danger',
        'gray'       => 'btn-secondary',
        'yellow'     => 'btn-warning',
        'light-blue' => 'btn-info',
        'white'      => '',
    ],
    'sizes'      => [
        'xsmall' => 'btn-xs',
        'small'  => 'btn-sm',
        'medium' => '',
        'large'  => 'btn-lg'
    ]
];
