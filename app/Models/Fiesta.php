<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'month',
    'day',
    'category',
    'honoree_name',
    'description',
    'liturgical_rank',
    'is_movable',
    'movable_rule',
    'is_active',
    'sort_order',
])]
class Fiesta extends Model
{
    use SoftDeletes;

    public const CATEGORY_CHRIST = 'christ';

    public const CATEGORY_FATHER = 'father';

    public const CATEGORY_MARY = 'mary';

    public const CATEGORY_SAINT = 'saint';

    public const CATEGORY_ANGEL = 'angel';

    protected $appends = ['feast_date'];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'day' => 'integer',
            'is_movable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getFeastDateAttribute(): ?string
    {
        if ($this->is_movable || ! $this->month || ! $this->day) {
            return null;
        }

        return sprintf('2000-%02d-%02d', $this->month, $this->day);
    }
}
