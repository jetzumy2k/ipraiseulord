<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'liturgical_date',
    'liturgical_season',
    'liturgical_color',
    'feast_name',
    'first_reading_reference',
    'first_reading_text',
    'second_reading_reference',
    'second_reading_text',
    'responsorial_psalm_reference',
    'responsorial_psalm_text',
    'alleluia_reference',
    'alleluia_text',
    'gospel_reference',
    'gospel_text',
    'liturgical_year',
])]
class MassGuide extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'liturgical_date' => 'date',
            'liturgical_year' => 'integer',
        ];
    }
}
