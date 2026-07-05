<?php

namespace Database\Seeders;

use App\Models\PageBanner;
use Illuminate\Database\Seeder;

class PageBannerSeeder extends Seeder
{
    /**
     * @var array<int, array{route_name: string, label: string, sort_order: int}>
     */
    protected array $pages = [
        ['route_name' => 'home', 'label' => 'Home', 'sort_order' => 1],
        ['route_name' => 'bible', 'label' => 'Holy Bible', 'sort_order' => 2],
        ['route_name' => 'bible-chapter', 'label' => 'Bible Chapter', 'sort_order' => 3],
        ['route_name' => 'mass-guide', 'label' => 'Mass Guide', 'sort_order' => 4],
        ['route_name' => 'mass-guide-date', 'label' => 'Mass Guide (Date)', 'sort_order' => 5],
        ['route_name' => 'fiesta-calendar', 'label' => 'Fiesta Calendar', 'sort_order' => 6],
        ['route_name' => 'novenas', 'label' => 'Novenas', 'sort_order' => 7],
        ['route_name' => 'novena-detail', 'label' => 'Novena Detail', 'sort_order' => 8],
        ['route_name' => 'prayers', 'label' => 'Prayers', 'sort_order' => 9],
        ['route_name' => 'prayer-detail', 'label' => 'Prayer Detail', 'sort_order' => 10],
        ['route_name' => 'ai-advice', 'label' => 'AI Advice', 'sort_order' => 11],
        ['route_name' => 'about', 'label' => 'About Us', 'sort_order' => 12],
        ['route_name' => 'privacy', 'label' => 'Privacy Policy', 'sort_order' => 13],
        ['route_name' => 'terms', 'label' => 'Terms & Conditions', 'sort_order' => 14],
        ['route_name' => 'contact', 'label' => 'Contact', 'sort_order' => 15],
        ['route_name' => 'donate', 'label' => 'Donate', 'sort_order' => 16],
    ];

    public function run(): void
    {
        foreach ($this->pages as $page) {
            PageBanner::query()->updateOrCreate(
                ['route_name' => $page['route_name']],
                [
                    'label' => $page['label'],
                    'sort_order' => $page['sort_order'],
                    'overlay_opacity' => 0.55,
                    'is_active' => true,
                ]
            );
        }
    }
}
