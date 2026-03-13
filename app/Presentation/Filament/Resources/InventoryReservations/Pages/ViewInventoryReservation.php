<?php

namespace App\Presentation\Filament\Resources\InventoryReservations\Pages;

use App\Presentation\Filament\Resources\InventoryReservations\InventoryReservationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryReservation extends ViewRecord
{
    protected static string $resource = InventoryReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
