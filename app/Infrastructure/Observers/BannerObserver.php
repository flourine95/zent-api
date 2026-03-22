<?php

namespace App\Infrastructure\Observers;

use App\Infrastructure\Models\Banner;
use Illuminate\Support\Facades\Cache;

class BannerObserver
{
    public function created(Banner $banner): void
    {
        $this->clearCache();
    }

    public function updated(Banner $banner): void
    {
        $this->clearCache();
    }

    public function deleted(Banner $banner): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::forget('api.config');
    }
}
