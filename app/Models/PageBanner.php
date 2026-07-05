<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'route_name',
    'label',
    'image_path',
    'overlay_opacity',
    'is_active',
    'sort_order',
])]
class PageBanner extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'overlay_opacity' => 'float',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
