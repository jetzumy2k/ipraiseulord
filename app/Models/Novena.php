<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'slug',
    'category',
    'patron_saint',
    'description',
    'opening_prayer',
    'closing_prayer',
    'duration_days',
    'is_active',
    'visit_count',
])]
class Novena extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'is_active' => 'boolean',
            'visit_count' => 'integer',
        ];
    }

    public function days(): HasMany
    {
        return $this->hasMany(NovenaDay::class)->orderBy('day_number');
    }
}
