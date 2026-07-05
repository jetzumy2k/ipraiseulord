<?php

namespace Database\Seeders;

use App\Models\SocialMediaSetting;
use Illuminate\Database\Seeder;

class SocialMediaSettingSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/praiseulord',
                'handle' => '@praiseulord',
                'display_locations' => ['header', 'footer'],
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://youtube.com/@praiseulord',
                'handle' => '@praiseulord',
                'display_locations' => ['footer'],
            ],
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/praiseulord',
                'handle' => '@praiseulord',
                'display_locations' => ['header', 'footer'],
            ],
        ];

        foreach ($platforms as $platform) {
            SocialMediaSetting::updateOrCreate(
                ['platform' => $platform['platform']],
                array_merge($platform, [
                    'show_share_buttons' => true,
                    'show_follow_links' => true,
                    'is_active' => true,
                ])
            );
        }
    }
}
