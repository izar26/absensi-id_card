<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default QrCode Generator
    |--------------------------------------------------------------------------
    |
    | This will be the default format used to generate QrCodes.
    |
    */
    'defaults' => [
        'renderer' => 'gd', // Menggunakan GD sebagai mesin gambar utama
        'renderer_options' => [],
        'format' => 'svg',
        'margin' => 1,
        'encoding' => 'UTF-8',
        'ecl' => 'M',
        'round_block_size' => true,
        'error_correction' => 'M',
        'logo' => false,
        'logo_path' => null,
        'logo_width' => 15,
        'logo_background_color' => null,
        'color' => [
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 255,
        ],
        'background_color' => [
            'red' => 255,
            'green' => 255,
            'blue' => 255,
            'alpha' => 255,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | QrCode Generators
    |--------------------------------------------------------------------------
    |
    | Here you may specify the generator to be used to create a QrCode.
    |
    */
    'generators' => [
        'svg' => [
            'renderer' => 'gd', // Menggunakan GD sebagai mesin gambar utama
            'renderer_options' => [],
            'margin' => 1,
            'encoding' => 'UTF-8',
            'ecl' => 'M',
            'round_block_size' => true,
            'color' => 'rgb(0, 0, 0)',
            'background_color' => 'rgb(255, 255, 255)',
        ],
        'png' => [
            'renderer' => 'gd', // Menggunakan GD sebagai mesin gambar utama
            'renderer_options' => [],
            'margin' => 1,
            'encoding' => 'UTF-8',
            'ecl' => 'M',
        ],
        'eps' => [
            'renderer' => 'gd', // Menggunakan GD sebagai mesin gambar utama
            'renderer_options' => [],
            'margin' => 1,
            'encoding' => 'UTF-8',
            'ecl' => 'M',
        ],
    ],
];