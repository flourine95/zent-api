<?php

namespace App\Domain\Product\Observers;

use App\Infrastructure\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::forget('api.config');
    }
}
