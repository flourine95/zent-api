<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                TextInput::make('price')
                    ->label('Giá bán')
                    ->required()
                    ->numeric()
                    ->prefix('₫')
                    ->minValue(0),

                TextInput::make('original_price')
                    ->label('Giá gốc')
                    ->numeric()
                    ->prefix('₫')
                    ->minValue(0)
                    ->helperText('Giá trước khi giảm (tùy chọn)'),

                Repeater::make('options')
                    ->label('Thuộc tính biến thể')
                    ->schema([
                        Select::make('attribute')
                            ->label('Thuộc tính')
                            ->options([
                                'Màu sắc' => 'Màu sắc',
                                'Kích thước' => 'Kích thước',
                                'Chất liệu' => 'Chất liệu',
                                'Kiểu dáng' => 'Kiểu dáng',
                            ])
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Tên thuộc tính')
                                    ->required(),
                            ])
                            ->createOptionUsing(fn (string $name): string => $name),

                        TextInput::make('value')
                            ->label('Giá trị')
                            ->required()
                            ->placeholder('VD: Đỏ, XL, Cotton'),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm thuộc tính')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => isset($state['attribute'], $state['value'])
                            ? "{$state['attribute']}: {$state['value']}"
                            : null
                    )
                    ->defaultItems(1)
                    ->columnSpanFull(),

                FileUpload::make('images')
                    ->label('Hình ảnh biến thể')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('products/variants')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                        '16:9',
                    ])
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->maxSize(2048)
                    ->maxFiles(10)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->downloadable()
                    ->openable()
                    ->helperText('Tối đa 10 ảnh, mỗi ảnh không quá 2MB')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                ImageColumn::make('images')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->size(60)
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->defaultImageUrl(url('/images/placeholder.svg')),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU đã được sao chép!'),

                TextColumn::make('options')
                    ->label('Thuộc tính')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }

                        return collect($state)
                            ->map(fn ($item) => is_array($item) && isset($item['attribute'], $item['value'])
                                    ? "{$item['attribute']}: {$item['value']}"
                                    : (is_array($item) ? implode(': ', $item) : $item)
                            )
                            ->join(', ');
                    })
                    ->wrap()
                    ->badge()
                    ->separator(','),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('original_price')
                    ->label('Giá gốc')
                    ->money('VND')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm biến thể')
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Sửa'),
                DeleteAction::make()
                    ->label('Xóa'),
                ForceDeleteAction::make()
                    ->label('Xóa vĩnh viễn'),
                RestoreAction::make()
                    ->label('Khôi phục'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                    ForceDeleteBulkAction::make()
                        ->label('Xóa vĩnh viễn đã chọn'),
                    RestoreBulkAction::make()
                        ->label('Khôi phục đã chọn'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
