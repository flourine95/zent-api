<?php

namespace App\Observers;

use App\Infrastructure\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->clearCache();
    }

    public function updated(Category $category): void
    {
        $this->clearCache();
    }

    public function deleted(Category $category): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::forget('api.config');
    }
}
