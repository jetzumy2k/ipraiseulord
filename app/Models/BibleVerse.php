<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bible_chapter_id', 'verse_number', 'text'])]
class BibleVerse extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'verse_number' => 'integer',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BibleChapter::class, 'bible_chapter_id');
    }
}
