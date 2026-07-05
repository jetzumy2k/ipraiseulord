<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'visitor_id',
    'page_type',
    'page_id',
    'page_slug',
    'page_title',
    'ip_address',
    'user_agent',
    'visited_at',
])]
class PageVisit extends Model
{
    protected function casts(): array
    {
        return [
            'page_id' => 'integer',
            'visited_at' => 'datetime',
        ];
    }
}
