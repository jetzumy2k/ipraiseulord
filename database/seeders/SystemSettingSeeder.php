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
            ['key' => 'seo_default_description', 'value' => 'Your Catholic companion online — daily Mass guides, Bible readings, novenas, prayers, and the fiesta calendar for the Philippines and the world.', 'group' => 'seo'],
            ['key' => 'seo_default_keywords', 'value' => 'catholic bible, mass guide, daily readings, novenas, prayers, fiesta calendar, liturgy, devotionals, philippines, filipino catholic', 'group' => 'seo'],
            ['key' => 'seo_home_headline', 'value' => 'Mass Guide, Novenas, Prayers, Fiesta Calendar & Bible', 'group' => 'seo'],
            ['key' => 'seo_page_meta', 'value' => '{}', 'group' => 'seo'],
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
