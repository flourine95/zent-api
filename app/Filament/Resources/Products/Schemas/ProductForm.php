<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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
                Section::make('Thông tin sản phẩm')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->autocomplete(false)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->helperText('URL thân thiện SEO')
                                    ->suffixAction(
                                        Action::make('generateSlug')
                                            ->icon('heroicon-m-arrow-path')
                                            ->action(function (Get $get, Set $set) {
                                                $name = $get('name');
                                                if ($name) {
                                                    $set('slug', Str::slug($name));
                                                }
                                            })
                                    ),

                                Select::make('category_id')
                                    ->label('Danh mục')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Chọn danh mục')
                                    ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->name),
                            ]),

                        RichEditor::make('description')
                            ->label('Mô tả sản phẩm')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3',
                                'bulletList', 'orderedList', 'blockquote',
                                'link', 'undo', 'redo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),

                        Repeater::make('specs')
                            ->label('Thông số kỹ thuật')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên thông số')
                                    ->required()
                                    ->placeholder('VD: Thương hiệu, Chất liệu, Xuất xứ'),

                                TextInput::make('value')
                                    ->label('Giá trị')
                                    ->required()
                                    ->placeholder('VD: Nike, Cotton, Việt Nam'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Thêm thông số')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),

                Section::make('Media & Trạng thái')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Hình ảnh đại diện')
                            ->image()
                            ->disk('public')
                            ->directory('products/thumbnails')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText('Kích thước tối đa: 2MB'),

                        Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Bật/tắt hiển thị sản phẩm'),

                        Placeholder::make('created_at')
                            ->label('Ngày tạo')
                            ->content(fn (?Product $record): string => $record?->created_at?->diffForHumans() ?? '-')
                            ->visible(fn (?Product $record): bool => $record !== null),

                        Placeholder::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->content(fn (?Product $record): string => $record?->updated_at?->diffForHumans() ?? '-')
                            ->visible(fn (?Product $record): bool => $record !== null),
                    ]),
            ]);
    }
}
