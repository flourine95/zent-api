<?php

namespace App\Shared\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearApiCache extends Command
{
    protected $signature = 'api:clear-cache {--key= : Specific cache key to clear}';

    protected $description = 'Clear API cache (config, categories, products)';

    public function handle(): int
    {
        if ($key = $this->option('key')) {
            Cache::forget($key);
            $this->info("Cache key '{$key}' cleared!");
        } else {
            // Clear all API related caches
            $keys = [
                'api.config',
                'settings.group.general',
                'settings.group.shipping',
                'settings.group.payment',
                'settings.group.contact',
                'settings.group.social',
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            $this->info('All API caches cleared!');
        }

        return self::SUCCESS;
    }
}
