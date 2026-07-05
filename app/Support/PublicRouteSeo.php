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
                'description' => 'Praise U Lord helps Catholics pray every day — Mass guides, Bible readings, novenas, traditional prayers, and a fiesta calendar in one place.',
                'og_type' => 'website',
            ],
            'bible' => [
                'path' => '/bible',
                'title' => 'Holy Bible',
                'description' => 'Read the Holy Bible online with multiple Catholic and Filipino translations. Browse books, chapters, and verses with easy navigation.',
            ],
            'mass-guide' => [
                'path' => '/mass-guide',
                'title' => 'Mass Guide',
                'description' => 'Today\'s Catholic Mass guide with readings, responses, feast day information, and the full order of Mass for priests and the faithful.',
            ],
            'fiesta-calendar' => [
                'path' => '/fiesta-calendar',
                'title' => 'Fiesta Calendar',
                'description' => 'Catholic fiesta calendar with feast days for Jesus Christ, the Holy Trinity, Mary, saints, and angels — perfect for parishes in the Philippines and abroad.',
            ],
            'novenas' => [
                'path' => '/novenas',
                'title' => 'Novenas',
                'description' => 'Nine-day Catholic novenas with leader and congregation responses — popular devotions to Our Lady, the Sacred Heart, and patron saints.',
            ],
            'prayers' => [
                'path' => '/prayers',
                'title' => 'Prayers',
                'description' => 'Classic Catholic prayers for morning, evening, and every occasion — including traditional Filipino and universal devotions.',
            ],
            'ai-advice' => [
                'path' => '/ai-advice',
                'title' => 'AI Spiritual Advice',
                'description' => 'Ask faith questions and receive answers rooted in Scripture and Catholic teaching, with Bible references for further reading.',
            ],
            'about' => [
                'path' => '/about',
                'title' => 'About Us',
                'description' => 'Learn about Praise U Lord — a Catholic ministry sharing the Gospel through Scripture, prayer, and liturgical resources online.',
                'static_slug' => 'about-us',
            ],
            'privacy' => [
                'path' => '/privacy',
                'title' => 'Privacy Policy',
                'description' => 'How Praise U Lord collects, uses, and protects your personal information when you use our Catholic prayer and Bible website.',
                'robots' => 'noindex, follow',
                'static_slug' => 'privacy-policy',
            ],
            'terms' => [
                'path' => '/terms',
                'title' => 'Terms and Conditions',
                'description' => 'Terms for using Praise U Lord, including acceptable use of our Catholic prayers, Mass guides, and spiritual content.',
                'robots' => 'noindex, follow',
                'static_slug' => 'terms-and-conditions',
            ],
            'contact' => [
                'path' => '/contact',
                'title' => 'Contact Us',
                'description' => 'Contact the Praise U Lord team for questions, prayer requests, feedback, or support with our Catholic resources.',
                'page_route' => 'contact',
            ],
            'donate' => [
                'path' => '/donate',
                'title' => 'Donate',
                'description' => 'Support Praise U Lord and help us maintain this free Catholic ministry of Bible reading, Mass guides, and daily prayer.',
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
