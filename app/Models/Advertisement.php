<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'type',
    'embed_script',
    'image_path',
    'url',
    'amount',
    'placements',
    'start_date',
    'end_date',
    'is_active',
    'impression_count',
    'click_count',
])]
class Advertisement extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'placements' => 'array',
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'impression_count' => 'integer',
            'click_count' => 'integer',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(AdInvoice::class);
    }
}
