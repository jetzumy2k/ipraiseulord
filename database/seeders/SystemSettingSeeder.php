<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Praise U Lord', 'group' => 'general'],
            ['key' => 'header_color', 'value' => '#1a365d', 'group' => 'appearance'],
            ['key' => 'footer_color', 'value' => '#2d3748', 'group' => 'appearance'],
            ['key' => 'show_date', 'value' => 'true', 'group' => 'display'],
            ['key' => 'show_temperature', 'value' => 'true', 'group' => 'display'],
            ['key' => 'weather_city', 'value' => 'Manila', 'group' => 'display'],
            ['key' => 'donations_enabled', 'value' => 'true', 'group' => 'donations'],
            ['key' => 'seo_default_description', 'value' => 'Read the Bible, follow daily Mass guides, novenas, prayers, and Catholic feast days on Praise U Lord.', 'group' => 'seo'],
            ['key' => 'seo_default_keywords', 'value' => 'bible, catholic, mass guide, novenas, prayers, fiesta calendar, scripture, devotionals', 'group' => 'seo'],
            ['key' => 'seo_og_image', 'value' => '/images/og-default.png', 'group' => 'seo'],
            ['key' => 'seo_twitter_handle', 'value' => '', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
