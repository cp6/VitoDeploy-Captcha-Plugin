<?php

return [
    'enabled' => env('VITO_CAPTCHA_ENABLED', true),
    'rate_limit' => (int) env('VITO_CAPTCHA_RATE_LIMIT', 30),

    'characters' => env('VITO_CAPTCHA_CHARACTERS', '2346789ABCDEFGHJKLMNPQRTUVWXYZ'),

    'heading' => env('VITO_CAPTCHA_HEADING', 'Log in to your account'),
    'description' => env('VITO_CAPTCHA_DESCRIPTION', 'Enter your credentials and complete the security check.'),
    'label' => env('VITO_CAPTCHA_LABEL', 'Security code'),
    'placeholder' => env('VITO_CAPTCHA_PLACEHOLDER', 'Enter the code shown'),
    'error_message' => env('VITO_CAPTCHA_ERROR', 'The security code is incorrect. Please try the new code.'),

    'image' => [
        'length' => (int) env('VITO_CAPTCHA_LENGTH', 5),
        'width' => (int) env('VITO_CAPTCHA_WIDTH', 220),
        'height' => (int) env('VITO_CAPTCHA_HEIGHT', 56),
        'quality' => (int) env('VITO_CAPTCHA_QUALITY', 90),
        'math' => env('VITO_CAPTCHA_MATH', false),
        'expire' => (int) env('VITO_CAPTCHA_EXPIRE', 120),
        'encrypt' => true,
        'sensitive' => env('VITO_CAPTCHA_CASE_SENSITIVE', false),
        'angle' => (int) env('VITO_CAPTCHA_ANGLE', 12),
        'lines' => (int) env('VITO_CAPTCHA_LINES', 3),
        'lineWidth' => 1,
        'lineColor' => env('VITO_CAPTCHA_LINE_COLOR', '#a1a1aa'),
        'bgImage' => false,
        'bgColor' => env('VITO_CAPTCHA_BACKGROUND_COLOR', '#f4f4f5'),
        'fontColors' => ['#18181b', '#312e81', '#4338ca'],
        'contrast' => 0,
        'sharpen' => 4,
        'blur' => 0,
    ],
];
