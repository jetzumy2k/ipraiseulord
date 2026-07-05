<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        $ads = [
            [
                'name' => 'Catholic Bookstore Banner',
                'type' => 'manual',
                'image_path' => '/images/ads/catholic-bookstore.jpg',
                'url' => 'https://example.com/catholic-bookstore',
                'amount' => 500.00,
                'placements' => ['banner', 'sidebar'],
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date' => now()->endOfYear()->toDateString(),
            ],
            [
                'name' => 'Parish Retreat Promotion',
                'type' => 'embed',
                'embed_script' => '<div class="ad-embed"><a href="https://example.com/retreat" target="_blank" rel="noopener"><strong>Annual Parish Retreat</strong> — Register now for a weekend of prayer and renewal.</a></div>',
                'url' => 'https://example.com/retreat',
                'amount' => 250.00,
                'placements' => ['sidebar', 'footer'],
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date' => null,
            ],
        ];

        foreach ($ads as $ad) {
            Advertisement::updateOrCreate(
                ['name' => $ad['name']],
                array_merge($ad, ['is_active' => true])
            );
        }
    }
}
