<?php

namespace App\Presentation\Filament\Resources\Categories\Pages;

use App\Presentation\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
