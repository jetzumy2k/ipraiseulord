<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['visitor_id', 'user_id', 'question', 'answer', 'bible_references', 'bible_version'])]
class AiConversation extends Model
{
    protected function casts(): array
    {
        return [
            'bible_references' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
