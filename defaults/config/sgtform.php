<?php

return [
    # What version of bootstrap should the app be using.
    'config'    => [
        'bootstrap' => [
            'version' => 4
        ]
    ],
    'bootstrap' =>
        [
            '3' => [
                'element'    =>
                    [
                        'link'   => [
                            'colors' =>
                                [
                                    'green'      => 'btn-success',
                                    'blue'       => 'btn-primary',
                                    'red'        => 'btn-danger',
                                    'gray'       => 'btn-secondary',
                                    'yellow'     => 'btn-warning',
                                    'light-blue' => 'btn-info',
                                    'white'      => '',
                                ],
                            'sizes'  => [
                                'xsmall' => 'btn-xs',
                                'small'  => 'btn-sm',
                                'medium' => '',
                                'large'  => 'btn-lg'
                            ]
                        ],
                        'button' => [
                            'colors' => [
                                'green'      => 'btn-success',
                                'blue'       => 'btn-primary',
                                'red'        => 'btn-danger',
                                'gray'       => 'btn-secondary',
                                'yellow'     => 'btn-warning',
                                'light-blue' => 'btn-info',
                                'white'      => '',
                            ],
                            'sizes'  => [
                                'xsmall' => 'btn-xs',
                                'small'  => 'btn-sm',
                                'medium' => '',
                                'large'  => 'btn-lg'
                            ]
                        ],
                        'view'   =>
                            [
                                'path' => 'sgtform::bootstrap.3.element.default'
                            ],
                        'input'  =>
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
                        'default' => 'sgtform::bootstrap.3.table.datatable.default'
                    ]
                ],
                'navigation' => [
                    'subbar' => 'sgtform::bootstrap.3.navigation.subbar',
                    'button' => [
                        'dropdown' => 'sgtform::bootstrap.3.navigation.button.dropdown'
                    ]
                ],
            ],
            '4' => [
                'element'    =>
                    [
                        'link'   => [
                            'colors' =>
                                [
                                    'green'      => 'btn-success',
                                    'blue'       => 'btn-primary',
                                    'red'        => 'btn-danger',
                                    'gray'       => 'btn-secondary',
                                    'yellow'     => 'btn-warning',
                                    'light-blue' => 'btn-info',
                                    'white'      => '',
                                ],
                            'sizes'  => [
                                'xsmall' => 'btn-xs',
                                'small'  => 'btn-sm',
                                'medium' => '',
                                'large'  => 'btn-lg'
                            ]
                        ],
                        'button' => [
                            'colors' => [
                                'green'      => 'btn-success',
                                'blue'       => 'btn-primary',
                                'red'        => 'btn-danger',
                                'gray'       => 'btn-secondary',
                                'yellow'     => 'btn-warning',
                                'light-blue' => 'btn-info',
                                'white'      => '',
                            ],
                            'sizes'  => [
                                'xsmall' => 'btn-xs',
                                'small'  => 'btn-sm',
                                'medium' => '',
                                'large'  => 'btn-lg'
                            ]
                        ],
                        'view'   =>
                            [
                                'path' => 'sgtform::bootstrap.4.element.default'
                            ],
                        'input'  =>
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
                        'default' => 'sgtform::bootstrap.4.table.datatable.default'
                    ]
                ],
                'navigation' => [
                    'subbar' => 'sgtform::bootstrap.4.navigation.subbar',
                    'button' => [
                        'dropdown' => 'sgtform::bootstrap.4.navigation.button.dropdown'
                    ]
                ],
            ]
        ]
];
