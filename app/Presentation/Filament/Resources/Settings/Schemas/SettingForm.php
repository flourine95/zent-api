<?php

namespace App\Presentation\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Unique identifier for this setting'),
                Textarea::make('value')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('The value of the setting'),
                TextInput::make('type')
                    ->required()
                    ->default('string')
                    ->datalist([
                        'string',
                        'number',
                        'boolean',
                        'json',
                    ])
                    ->helperText('Data type: string, number, boolean, json'),
                TextInput::make('group')
                    ->required()
                    ->default('general')
                    ->datalist([
                        'general',
                        'shipping',
                        'payment',
                        'contact',
                        'social',
                        'order',
                    ])
                    ->helperText('Group for organizing settings'),
                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Description of what this setting does'),
            ]);
    }
}
