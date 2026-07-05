<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class WeatherService
{
    public function getDisplayTemperature(): ?string
    {
        $manual = SystemSetting::query()->where('key', 'temperature')->value('value');

        if ($manual !== null && $manual !== '' && ! str_contains($manual, '—')) {
            return $manual;
        }

        $weather = $this->getCurrentWeather();

        if ($weather === null) {
            return null;
        }

        return round($weather['celsius']).' °C';
    }

    /**
     * @return array{celsius: float, city: string}|null
     */
    public function getCurrentWeather(): ?array
    {
        $city = trim((string) (SystemSetting::query()->where('key', 'weather_city')->value('value') ?: 'Manila'));

        if ($city === '') {
            return null;
        }

        $cacheKey = 'weather:'.md5(strtolower($city));

        return Cache::remember($cacheKey, now()->addMinutes(30), fn () => $this->fetchWeatherForCity($city));
    }

    /**
     * @return array{celsius: float, city: string}|null
     */
    protected function fetchWeatherForCity(string $city): ?array
    {
        try {
            $geo = Http::timeout(8)->get('https://geocoding-api.open-meteo.com/v1/search', [
                'name' => $city,
                'count' => 1,
                'language' => 'en',
                'format' => 'json',
            ]);

            if (! $geo->successful()) {
                return null;
            }

            $result = $geo->json('results.0');

            if (! is_array($result)) {
                return null;
            }

            $weather = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $result['latitude'],
                'longitude' => $result['longitude'],
                'current' => 'temperature_2m',
                'timezone' => 'auto',
            ]);

            if (! $weather->successful()) {
                return null;
            }

            $celsius = $weather->json('current.temperature_2m');

            if ($celsius === null) {
                return null;
            }

            return [
                'celsius' => (float) $celsius,
                'city' => (string) ($result['name'] ?? $city),
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
