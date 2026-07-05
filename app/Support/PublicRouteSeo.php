<?php

namespace App\Support;

class PublicRouteSeo
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function routes(): array
    {
        return [
            'home' => [
                'path' => '/',
                'title' => 'Home',
                'description' => 'Welcome to Praise U Lord — read the Bible, follow the daily Mass guide, explore novenas, prayers, and the Catholic fiesta calendar.',
                'og_type' => 'website',
            ],
            'bible' => [
                'path' => '/bible',
                'title' => 'Holy Bible',
                'description' => 'Read the complete Holy Bible online in many languages and translations, including Catholic and Filipino editions.',
            ],
            'mass-guide' => [
                'path' => '/mass-guide',
                'title' => 'Mass Guide',
                'description' => 'Follow the full order of Mass with priest and people responses, daily readings, and liturgical feast information.',
            ],
            'fiesta-calendar' => [
                'path' => '/fiesta-calendar',
                'title' => 'Fiesta Calendar',
                'description' => 'Browse Catholic feast days for Jesus Christ, the Holy Trinity, Mary, saints, and angels throughout the year.',
            ],
            'novenas' => [
                'path' => '/novenas',
                'title' => 'Novenas',
                'description' => 'Nine-day Catholic novenas with leader and congregation responses for popular devotions and patron saints.',
            ],
            'prayers' => [
                'path' => '/prayers',
                'title' => 'Prayers',
                'description' => 'Traditional and common Catholic prayers for daily devotion, worship, and spiritual growth.',
            ],
            'ai-advice' => [
                'path' => '/ai-advice',
                'title' => 'AI Advice',
                'description' => 'Ask faith questions and receive answers grounded in Bible verses and Catholic teaching.',
            ],
            'about' => [
                'path' => '/about',
                'title' => 'About Us',
                'description' => 'Learn about Praise U Lord and our mission to share the Gospel through Bible reading and Catholic faith resources.',
                'static_slug' => 'about-us',
            ],
            'privacy' => [
                'path' => '/privacy',
                'title' => 'Privacy Policy',
                'description' => 'Read how Praise U Lord collects, uses, and protects your information.',
                'robots' => 'noindex, follow',
                'static_slug' => 'privacy-policy',
            ],
            'terms' => [
                'path' => '/terms',
                'title' => 'Terms and Conditions',
                'description' => 'Review the terms and conditions for using Praise U Lord.',
                'robots' => 'noindex, follow',
                'static_slug' => 'terms-and-conditions',
            ],
            'contact' => [
                'path' => '/contact',
                'title' => 'Contact',
                'description' => 'Get in touch with the Praise U Lord team for questions, feedback, and support.',
                'page_route' => 'contact',
            ],
            'donate' => [
                'path' => '/donate',
                'title' => 'Donate',
                'description' => 'Support Praise U Lord and help us share the Gospel through Bible reading and Catholic faith resources.',
                'page_route' => 'donate',
            ],
        ];
    }

    public static function findByPath(string $path): ?array
    {
        $normalized = '/'.trim($path, '/');
        if ($normalized !== '/') {
            $normalized = rtrim($normalized, '/');
        }

        foreach (self::routes() as $key => $route) {
            if ($route['path'] === $normalized) {
                return ['key' => $key, ...$route];
            }
        }

        return null;
    }
}
