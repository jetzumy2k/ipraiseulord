<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bible_book_id', 'chapter_number', 'verse_count'])]
class BibleChapter extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'chapter_number' => 'integer',
            'verse_count' => 'integer',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BibleBook::class, 'bible_book_id');
    }

    public function verses(): HasMany
    {
        return $this->hasMany(BibleVerse::class);
    }
}
