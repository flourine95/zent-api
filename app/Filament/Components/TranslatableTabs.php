<?php

namespace App\Filament\Components;

use App\Helpers\LocaleHelper;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class TranslatableTabs
{
    /**
     * Tạo tabs cho translatable fields
     * 
     * @param array $fields ['name' => 'text', 'description' => 'textarea']
     * @param array $labels ['name' => 'Tên', 'description' => 'Mô tả']
     */
    public static function make(array $fields, array $labels = []): Tabs
    {
        $tabs = [];

        foreach (LocaleHelper::available() as $code => $locale) {
            $tabs[] = Tab::make("{$locale['flag']} {$locale['name']}")
                ->schema(self::buildFields($fields, $labels, $code, $locale['required']));
        }

        return Tabs::make('Translations')->tabs($tabs);
    }

    /**
     * Build fields cho một locale
     */
    protected static function buildFields(array $fields, array $labels, string $locale, bool $required): array
    {
        $components = [];

        foreach ($fields as $field => $type) {
            $label = $labels[$field] ?? ucfirst($field);
            $fieldName = "{$field}.{$locale}";

            $component = match ($type) {
                'textarea' => Textarea::make($fieldName)
                    ->label($label)
                    ->rows(4)
                    ->maxLength(1000),
                    
                'text' => TextInput::make($fieldName)
                    ->label($label)
                    ->maxLength(255),
                    
                'richtext' => \Filament\Forms\Components\RichEditor::make($fieldName)
                    ->label($label),
                    
                default => TextInput::make($fieldName)->label($label),
            };

            // Chỉ required cho locale bắt buộc
            if ($required) {
                $component->required();
            }

            $components[] = $component;
        }

        return $components;
    }

    /**
     * Tạo inline fields (Grid) cho short fields
     */
    public static function inline(string $field, string $label, string $type = 'text'): array
    {
        $locales = LocaleHelper::available();
        $columns = count($locales);

        $fields = [];
        foreach ($locales as $code => $locale) {
            $fieldName = "{$field}.{$code}";
            $fieldLabel = "{$locale['flag']} {$label}";

            $component = match ($type) {
                'textarea' => Textarea::make($fieldName)
                    ->label($fieldLabel)
                    ->rows(3),
                    
                default => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->maxLength(255),
            };

            if ($locale['required']) {
                $component->required();
            }

            $fields[] = $component;
        }

        return [
            \Filament\Schemas\Components\Grid::make($columns)->schema($fields)
        ];
    }
}
