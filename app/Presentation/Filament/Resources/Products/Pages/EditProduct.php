<?php

namespace App\Presentation\Filament\Resources\Products\Pages;

use App\Presentation\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            \Filament\Actions\Action::make('viewProduct')
                ->label('Xem sản phẩm')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => ProductResource::getUrl('view', ['record' => $record]))
                ->color('gray'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được cập nhật';
    }
}
