<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | Danh sách các ngôn ngữ được hỗ trợ trong hệ thống.
    | Để thêm ngôn ngữ mới, chỉ cần thêm vào array này.
    |
    */
    'available' => [
        'vi' => [
            'name' => 'Tiếng Việt',
            'native' => 'Tiếng Việt',
            'flag' => '🇻🇳',
            'required' => true, // Bắt buộc phải có translation
            'enabled' => true,
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'required' => false,
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | Ngôn ngữ mặc định của hệ thống.
    |
    */
    'default' => env('APP_LOCALE', 'vi'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | Ngôn ngữ dự phòng khi translation không tồn tại.
    |
    */
    'fallback' => env('APP_FALLBACK_LOCALE', 'en'),
];
