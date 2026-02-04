<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Thông tin tag')
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs([
                                Tab::make('Tiếng Việt')
                                    ->icon('heroicon-o-language')
                                    ->schema([
                                        TextInput::make('name.vi')
                                            ->label('Tên tag (VI)')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ví dụ: Công nghệ, Thời trang'),
                                    ]),
                                
                                Tab::make('English')
                                    ->icon('heroicon-o-language')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Tag Name (EN)')
                                            ->maxLength(255)
                                            ->placeholder('e.g., Technology, Fashion'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
