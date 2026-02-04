<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Components\TranslatableTabs;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Section::make(__('resources.products.sections.info'))
                    ->columnSpan(2)
                    ->schema([
                        // Sử dụng TranslatableTabs component
                        TranslatableTabs::make(
                            fields: [
                                'name' => 'text',
                                'description' => 'textarea',
                            ],
                            labels: [
                                'name' => __('resources.products.fields.name'),
                                'description' => __('resources.products.fields.description'),
                            ]
                        ),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label(__('resources.products.fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->helperText(__('resources.products.fields.slug_helper'))
                                    ->suffixAction(
                                        Action::make('generateSlug')
                                            ->icon('heroicon-m-arrow-path')
                                            ->action(function (Get $get, Set $set) {
                                                // Lấy name từ locale mặc định
                                                $defaultLocale = config('locales.default', 'vi');
                                                $name = $get("name.{$defaultLocale}");
                                                $set('slug', Str::slug($name));
                                            })
                                    ),

                                Select::make('category_id')
                                    ->label(__('resources.products.fields.category'))
                                    ->relationship('category', 'name', modifyQueryUsing: fn ($query) => $query->orderByRaw("name->>'vi'"))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder(__('resources.products.fields.category_placeholder'))
                                    ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->name),
                            ]),

                        KeyValue::make('specs')
                            ->label(__('resources.products.fields.specs'))
                            ->keyLabel(__('resources.products.fields.specs_key'))
                            ->valueLabel(__('resources.products.fields.specs_value'))
                            ->addActionLabel(__('resources.products.fields.specs_add'))
                            ->helperText(__('resources.products.fields.specs_helper'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('resources.products.sections.media'))
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label(__('resources.products.fields.thumbnail'))
                            ->image()
                            ->directory('products')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText(__('resources.products.fields.thumbnail_helper')),

                        Toggle::make('is_active')
                            ->label(__('resources.products.fields.is_active'))
                            ->default(true)
                            ->helperText(__('resources.products.fields.is_active_helper')),
                    ]),
            ]);
    }
}
