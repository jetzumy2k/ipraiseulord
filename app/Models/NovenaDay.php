<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['novena_id', 'day_number', 'title', 'content', 'prayer'])]
class NovenaDay extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
        ];
    }

    public function novena(): BelongsTo
    {
        return $this->belongsTo(Novena::class);
    }
}
