<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Section::make('Thông tin cơ bản')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên danh mục')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->maxLength(500),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Category::class, 'slug', ignoreRecord: true)
                                    ->helperText('URL thân thiện SEO (tự động tạo từ tên)')
                                    ->suffixAction(
                                        Action::make('generateSlug')
                                            ->icon('heroicon-m-arrow-path')
                                            ->action(function (Get $get, Set $set) {
                                                $set('slug', Str::slug($get('name')));
                                            })
                                    ),

                                Select::make('parent_id')
                                    ->label('Danh mục cha')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Chọn danh mục cha (để trống nếu là danh mục gốc)')
                                    ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->full_name)
                                    ->options(function (?Category $record) {
                                        $query = Category::query()->orderBy('name');

                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }

                                        return $query->get()
                                            ->mapWithKeys(fn ($cat) => [$cat->id => $cat->full_name]);
                                    }),
                            ]),
                    ]),

                Section::make('Hiển thị & Media')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Hình ảnh')
                            ->image()
                            ->disk('public')
                            ->directory('categories')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->downloadable()
                            ->openable()
                            ->helperText('Kích thước tối đa: 2MB'),

                        Toggle::make('is_visible')
                            ->label('Hiển thị công khai')
                            ->default(true)
                            ->helperText('Bật/tắt hiển thị danh mục này trên website'),
                    ]),
            ]);
    }
}
