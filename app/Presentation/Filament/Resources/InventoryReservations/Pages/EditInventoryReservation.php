<?php

namespace App\Presentation\Filament\Resources\InventoryReservations\Pages;

use App\Presentation\Filament\Resources\InventoryReservations\InventoryReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryReservation extends EditRecord
{
    protected static string $resource = InventoryReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
