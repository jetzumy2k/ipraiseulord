<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'platform',
    'url',
    'handle',
    'display_locations',
    'show_share_buttons',
    'show_follow_links',
    'is_active',
])]
class SocialMediaSetting extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'display_locations' => 'array',
            'show_share_buttons' => 'boolean',
            'show_follow_links' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
