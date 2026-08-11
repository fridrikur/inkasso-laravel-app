<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Hent en indstilling (med cache)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            // 🟢 Brug explicit where('key', $key) i stedet for find($key)
            $setting = SystemSetting::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            $val = $setting->value;

            // Konverter boolske streng-værdier
            if ($val === 'true' || $val === '1') {
                return true;
            }
            if ($val === 'false' || $val === '0') {
                return false;
            }

            return $val;
        });
    }

    /**
     * Gem/opdatér en indstilling
     */
    public function set(string $key, mixed $value): void
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        // Nulstil cachen for denne nøgle
        Cache::forget("setting.{$key}");
    }

    /**
     * Slet en indstilling
     */
    public function forget(string $key): void
    {
        SystemSetting::where('key', $key)->delete();
        Cache::forget("setting.{$key}");
    }
}