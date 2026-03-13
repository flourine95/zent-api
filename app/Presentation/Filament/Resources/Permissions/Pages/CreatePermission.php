<?php

namespace App\Presentation\Filament\Resources\Permissions\Pages;

use App\Presentation\Filament\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
