<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Lấy tất cả locales có sẵn
     */
    public static function available(): array
    {
        return array_filter(
            config('locales.available', []),
            fn($locale) => $locale['enabled'] ?? true
        );
    }

    /**
     * Lấy danh sách locale codes
     */
    public static function codes(): array
    {
        return array_keys(self::available());
    }

    /**
     * Lấy locale mặc định
     */
    public static function default(): string
    {
        return config('locales.default', 'vi');
    }

    /**
     * Lấy fallback locale
     */
    public static function fallback(): string
    {
        return config('locales.fallback', 'en');
    }

    /**
     * Kiểm tra locale có hợp lệ không
     */
    public static function isValid(string $locale): bool
    {
        return in_array($locale, self::codes());
    }

    /**
     * Lấy thông tin locale
     */
    public static function get(string $locale): ?array
    {
        return self::available()[$locale] ?? null;
    }

    /**
     * Lấy locales bắt buộc phải có translation
     */
    public static function required(): array
    {
        return array_keys(
            array_filter(
                self::available(),
                fn($locale) => $locale['required'] ?? false
            )
        );
    }

    /**
     * Lấy locales optional
     */
    public static function optional(): array
    {
        return array_keys(
            array_filter(
                self::available(),
                fn($locale) => !($locale['required'] ?? false)
            )
        );
    }
}
