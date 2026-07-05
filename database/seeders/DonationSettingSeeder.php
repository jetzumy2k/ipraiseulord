<?php

namespace Database\Seeders;

use App\Models\DonationSetting;
use Illuminate\Database\Seeder;

class DonationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'type' => 'bank',
                'label' => 'Bank Transfer',
                'account_name' => 'Praise U Lord Ministry',
                'account_number' => '1234-5678-9012',
                'bank_name' => 'BDO Unibank',
                'instructions' => 'Please include your name and email in the transfer reference so we may acknowledge your gift.',
                'display_locations' => ['footer', 'donation_page'],
                'sort_order' => 1,
            ],
            [
                'type' => 'paypal',
                'label' => 'PayPal',
                'paypal_email' => 'donations@praiseulord.com',
                'instructions' => 'Send your donation via PayPal to the email address above. May God bless your generosity!',
                'display_locations' => ['footer', 'donation_page'],
                'sort_order' => 2,
            ],
            [
                'type' => 'ewallet',
                'label' => 'GCash',
                'ewallet_provider' => 'GCash',
                'ewallet_number' => '09171234567',
                'account_name' => 'Praise U Lord Ministry',
                'instructions' => 'Scan our QR code or send to the GCash number above. Please screenshot your receipt and email admin@praiseulord.com.',
                'display_locations' => ['footer', 'donation_page'],
                'sort_order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            DonationSetting::updateOrCreate(
                [
                    'type' => $setting['type'],
                    'label' => $setting['label'],
                ],
                array_merge($setting, ['is_active' => true])
            );
        }
    }
}
