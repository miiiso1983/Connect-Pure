<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Settings
{
    protected string $cacheKey = 'system_settings';

    protected int $ttl = 3600; // 1 hour

    public function all(): array
    {
        if (! Schema::hasTable('system_settings')) {
            return [];
        }

        return Cache::remember($this->cacheKey, $this->ttl, function () {
            return DB::table('system_settings')->pluck('value', 'key')->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    public function getMany(array $keys): array
    {
        $settings = $this->all();
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return $result;
    }

    public function set(string $key, $value): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        DB::table('system_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );

        $this->forgetCache();
    }

    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function forgetCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
