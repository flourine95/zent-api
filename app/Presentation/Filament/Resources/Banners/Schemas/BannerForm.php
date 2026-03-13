<?php

namespace App\Presentation\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->directory('banners')
                    ->visibility('public')
                    ->required(),
                TextInput::make('link')
                    ->url()
                    ->maxLength(255),
                TextInput::make('button_text')
                    ->maxLength(255),
                TextInput::make('position')
                    ->required()
                    ->default('home_hero')
                    ->datalist([
                        'home_hero',
                        'home_secondary',
                        'category_top',
                        'product_detail',
                    ]),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                DateTimePicker::make('start_date')
                    ->label('Start Date (Optional)'),
                DateTimePicker::make('end_date')
                    ->label('End Date (Optional)'),
            ]);
    }
}
