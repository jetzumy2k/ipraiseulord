<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\MassGuide;

class MassGuideController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return MassGuide::class;
    }

    protected function searchableFields(): array
    {
        return ['feast_name', 'liturgical_season', 'gospel_reference', 'first_reading_reference'];
    }

    protected function filterableFields(): array
    {
        return ['liturgical_season', 'liturgical_year', 'liturgical_color'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'liturgical_date', 'liturgical_year', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return [
            'liturgical_date' => ['required', 'date', 'unique:mass_guides,liturgical_date'],
            'liturgical_season' => ['nullable', 'string', 'max:255'],
            'liturgical_color' => ['nullable', 'string', 'max:255'],
            'feast_name' => ['nullable', 'string', 'max:255'],
            'first_reading_reference' => ['nullable', 'string', 'max:255'],
            'first_reading_text' => ['nullable', 'string'],
            'second_reading_reference' => ['nullable', 'string', 'max:255'],
            'second_reading_text' => ['nullable', 'string'],
            'responsorial_psalm_reference' => ['nullable', 'string', 'max:255'],
            'responsorial_psalm_text' => ['nullable', 'string'],
            'alleluia_reference' => ['nullable', 'string', 'max:255'],
            'alleluia_text' => ['nullable', 'string'],
            'gospel_reference' => ['nullable', 'string', 'max:255'],
            'gospel_text' => ['nullable', 'string'],
            'liturgical_year' => ['nullable', 'integer'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'liturgical_date' => ['sometimes', 'date', 'unique:mass_guides,liturgical_date,'.$id],
            'liturgical_season' => ['nullable', 'string', 'max:255'],
            'liturgical_color' => ['nullable', 'string', 'max:255'],
            'feast_name' => ['nullable', 'string', 'max:255'],
            'first_reading_reference' => ['nullable', 'string', 'max:255'],
            'first_reading_text' => ['nullable', 'string'],
            'second_reading_reference' => ['nullable', 'string', 'max:255'],
            'second_reading_text' => ['nullable', 'string'],
            'responsorial_psalm_reference' => ['nullable', 'string', 'max:255'],
            'responsorial_psalm_text' => ['nullable', 'string'],
            'alleluia_reference' => ['nullable', 'string', 'max:255'],
            'alleluia_text' => ['nullable', 'string'],
            'gospel_reference' => ['nullable', 'string', 'max:255'],
            'gospel_text' => ['nullable', 'string'],
            'liturgical_year' => ['nullable', 'integer'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['liturgical_date'];
    }
}
