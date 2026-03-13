<?php

namespace App\Presentation\Filament\Resources\InventoryReservations\Pages;

use App\Presentation\Filament\Resources\InventoryReservations\InventoryReservationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryReservations extends ListRecords
{
    protected static string $resource = InventoryReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
