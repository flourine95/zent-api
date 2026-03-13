<?php

namespace App\Domain\Config\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public function created(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    public function updated(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    public function deleted(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    protected function clearCache(Setting $setting): void
    {
        Cache::forget("setting.{$setting->key}");
        Cache::forget("settings.group.{$setting->group}");
        Cache::forget('api.config');
    }
}
