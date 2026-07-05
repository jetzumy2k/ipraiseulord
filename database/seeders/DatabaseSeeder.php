<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('INSTALL_ADMIN_EMAIL', 'admin@praiseulord.com')],
            [
                'name' => env('INSTALL_ADMIN_NAME', 'Super Admin'),
                'password' => env('INSTALL_ADMIN_PASSWORD', 'password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            SystemSettingSeeder::class,
            StaticPageSeeder::class,
            BibleSeeder::class,
            MassGuideSeeder::class,
            FiestaSeeder::class,
            NovenaSeeder::class,
            PrayerSeeder::class,
            ProverbSeeder::class,
            DailyPsalmSeeder::class,
            DonationSettingSeeder::class,
            PageBannerSeeder::class,
            SocialMediaSettingSeeder::class,
            AdvertisementSeeder::class,
            AdInvoiceSeeder::class,
        ]);
    }
}
