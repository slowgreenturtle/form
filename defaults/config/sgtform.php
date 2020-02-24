<?php

return [
    'database'  => [
        'copy'   => [
            'cloud' => [
                'path'     => 'database',
                'filename' => 'database',
                'disk'     => 's3'  #$this is the disk setup in the Laravel filesystems config.
            ],
            'local' => [
                'filename' => 'database',
                'path'     => 'database'
            ],
        ],
        'tenant' => [
            'enabled'    => false,
            'connection' => 'team',
        ],
        'backup' => [
            'path'        => 'database', # this is assumed to be relative from the Laravel storage path.
            'days_stored' => 1,
            's3_path'     => 'backup',
        ]
    ],
    # What version of bootstrap should the app be using.
    'config'    => [
        'change_table' => 'model_changes',
        'bootstrap'    => [
            'version' => 4,
        ],
        'table'        => [
            'settings' => [
                "lengthChange" => true,  # allow the user to change the page length display
                "paging"       => true,   # whether paging is allowed or all records are displayed
                "searching"    => true,   # whether the search box is displayed.
                "pageLength"   => 10,
                "lengthMenu"   => [10, 25, 50]
            ]
        ]
    ],
    'bootstrap' => [
        '3' => [
            'element'    =>
                [
                    'css'    => [
                        'error' => [
                            'has-error'
                        ]
                    ],
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
                        ],
                        'css'    => [
                            'div'     => [
                                'form-group'
                            ],
                            'element' => [
                                'form-control',
                                'btn',
                                'btn-success'
                            ],
                        ]
                    ],
                    'view'   =>
                        [
                            'path' => 'sgtform::bootstrap.3.element.default'
                        ],
                    'select' =>
                        [
                            'css' => [
                                'div'     => [
                                    'form-group'
                                ],
                                'element' => [
                                    'form-control'
                                ],
                            ]
                        ],
                    'input'  =>
                        [
                            'css' => [
                                'div'     => [
                                    'form-group'
                                ],
                                'element' => [
                                    'form-control'
                                ],
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
            'element'    => [
                'link'   => [
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
                    ],
                ],
                'view'   => [
                    'path' => 'sgtform::bootstrap.4.element.default'
                ],
                'input'  => [
                    'css' => [
                        'div'     => [
                            'form-group'

                        ],
                        'element' => [
                            'form-control'],
                        'error'   => [
                            'is-invalid'
                        ]
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
