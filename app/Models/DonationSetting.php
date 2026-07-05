<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'type',
    'label',
    'account_name',
    'account_number',
    'bank_name',
    'paypal_email',
    'ewallet_provider',
    'ewallet_number',
    'qr_code_path',
    'instructions',
    'display_locations',
    'is_active',
    'sort_order',
])]
class DonationSetting extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'display_locations' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
