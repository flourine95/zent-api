<?php

namespace App\Presentation\Filament\Resources\Users\Pages;

use App\Presentation\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
