<?php

namespace App\Presentation\Filament\Resources\Settings\Pages;

use App\Presentation\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
