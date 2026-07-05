<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'psalm_number', 'text', 'reference', 'is_active', 'visit_count'])]
class DailyPsalm extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'psalm_number' => 'integer',
            'is_active' => 'boolean',
            'visit_count' => 'integer',
        ];
    }
}
