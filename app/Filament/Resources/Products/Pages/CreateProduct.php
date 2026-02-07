<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được tạo thành công';
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Sản phẩm đã được tạo')
            ->body('Bạn có thể thêm biến thể cho sản phẩm ngay bây giờ.')
            ->duration(5000);
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Thông tin cơ bản')
                ->description('Tên, slug và danh mục sản phẩm')
                ->icon('heroicon-o-information-circle')
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

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
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

                    Toggle::make('is_active')
                        ->label('Kích hoạt')
                        ->default(true)
                        ->inline(false)
                        ->helperText('Bật/tắt hiển thị sản phẩm'),
                ]),

            Step::make('Mô tả & Hình ảnh')
                ->description('Mô tả chi tiết và hình ảnh sản phẩm')
                ->icon('heroicon-o-photo')
                ->schema([
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
                        ->helperText('Kích thước tối đa: 2MB')
                        ->columnSpanFull(),
                ]),

            Step::make('Thông số kỹ thuật')
                ->description('Các thông số chi tiết của sản phẩm')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Repeater::make('specs')
                        ->label('Thông số kỹ thuật')
                        ->schema([
                            TextInput::make('name')
                                ->label('Tên thông số')
                                ->required()
                                ->placeholder('VD: Thương hiệu, Chất liệu, Xuất xứ')
                                ->distinct()
                                ->validationMessages([
                                    'distinct' => 'Tên thông số đã tồn tại.',
                                ]),

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
                ])
                ->completedIcon('heroicon-o-check-circle'),
        ];
    }
}
