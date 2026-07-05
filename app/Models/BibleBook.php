<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bible_version_id', 'name', 'abbreviation', 'testament', 'book_order', 'chapter_count'])]
class BibleBook extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'book_order' => 'integer',
            'chapter_count' => 'integer',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(BibleVersion::class, 'bible_version_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(BibleChapter::class);
    }
}
