<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optimized upload defaults
    |--------------------------------------------------------------------------
    |
    | All image uploads are resized/compressed on the server before storage so
    | the public site stays fast. Accept larger originals in forms; storage
    | output is capped near max_bytes.
    |
    */
    'max_bytes' => (int) env('IMAGE_MAX_KB', 700) * 1024,
    'max_width' => (int) env('IMAGE_MAX_WIDTH', 2200),
    'max_height' => (int) env('IMAGE_MAX_HEIGHT', 2200),
    'initial_quality' => 85,
    'min_quality' => 58,
    'png_compression' => 7,

    'presets' => [
        'portrait' => [
            'max_width' => 1400,
            'max_height' => 2100,
        ],
        'hero' => [
            'max_width' => 2400,
            'max_height' => 1350,
        ],
        'logo' => [
            'max_width' => 900,
            'max_height' => 900,
            'max_bytes' => 350 * 1024,
        ],
        'thumb' => [
            'max_width' => 1200,
            'max_height' => 1200,
        ],
    ],
];
