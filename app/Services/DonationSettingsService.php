<?php

namespace App\Services;

use App\Models\DonationSetting;
use App\Models\SystemSetting;

class DonationSettingsService
{
    public const GLOBAL_KEY = 'donations_enabled';

    public static function isGloballyEnabled(): bool
    {
        $value = SystemSetting::query()
            ->where('key', self::GLOBAL_KEY)
            ->value('value');

        if ($value === null) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function setGloballyEnabled(bool $enabled): void
    {
        SystemSetting::updateOrCreate(
            ['key' => self::GLOBAL_KEY],
            ['value' => $enabled ? 'true' : 'false', 'group' => 'donations']
        );
    }

    /**
     * @return array{donations_enabled: bool, active_count: int, total_count: int}
     */
    public static function statusSummary(): array
    {
        return [
            'donations_enabled' => self::isGloballyEnabled(),
            'active_count' => DonationSetting::query()->where('is_active', true)->count(),
            'total_count' => DonationSetting::query()->count(),
        ];
    }

    public static function publicDonations()
    {
        if (! self::isGloballyEnabled()) {
            return collect();
        }

        $donations = DonationSetting::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $donations->each(function (DonationSetting $donation): void {
            if (! $donation->qr_code_path) {
                return;
            }

            $donation->account_number = null;
            $donation->ewallet_number = null;
            $donation->paypal_email = null;
        });
    }
}
