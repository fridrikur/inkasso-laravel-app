<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Hent en indstilling (med cache & fejlhåndtering hvis DB mangler)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
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
        } catch (\Throwable $e) {
            // Returner defaultværdi hvis DB/tabel endnu ikke eksisterer
            return $default;
        }
    }

    /**
     * Gem/opdatér en indstilling
     */
    public function set(string $key, mixed $value): void
    {
        try {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );

            // Nulstil cachen for denne nøgle
            Cache::forget("setting.{$key}");
        } catch (\Throwable $e) {
            // Ignorer hvis DB endnu ikke er oprettet
        }
    }

    /**
     * Slet en indstilling
     */
    public function forget(string $key): void
    {
        try {
            SystemSetting::where('key', $key)->delete();
            Cache::forget("setting.{$key}");
        } catch (\Throwable $e) {
            // Ignorer hvis DB endnu ikke er oprettet
        }
    }
}