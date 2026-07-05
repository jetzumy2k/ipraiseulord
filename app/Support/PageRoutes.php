<?php

namespace App\Support;

class PageRoutes
{
    public const ROUTES = [
        'home' => 'Home',
        'donate' => 'Donate',
        'contact' => 'Contact',
        'bible' => 'Holy Bible',
        'mass-guide' => 'Mass Guide',
        'fiesta-calendar' => 'Fiesta Calendar',
        'novenas' => 'Novenas',
        'prayers' => 'Prayers',
        'ai-advice' => 'AI Advice',
    ];

    public static function labels(): array
    {
        return self::ROUTES;
    }

    public static function isValid(?string $route): bool
    {
        if ($route === null || $route === '') {
            return true;
        }

        return array_key_exists($route, self::ROUTES);
    }
}
