<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\Novena;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NovenaController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return Novena::class;
    }

    protected function defaultRelations(): array
    {
        return ['days'];
    }

    protected function searchableFields(): array
    {
        return ['title', 'slug', 'patron_saint', 'description'];
    }

    protected function filterableFields(): array
    {
        return ['category', 'is_active'];
    }

    public function store(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), array_merge($this->storeRules(), [
            'days' => ['nullable', 'array'],
            'days.*.day_number' => ['required_with:days', 'integer', 'min:1'],
            'days.*.title' => ['nullable', 'string', 'max:255'],
            'days.*.content' => ['required_with:days', 'string'],
            'days.*.prayer' => ['nullable', 'string'],
        ]))->validate();

        $days = $data['days'] ?? [];
        unset($data['days']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $novena = DB::transaction(function () use ($data, $days) {
            $novena = Novena::create($data);

            foreach ($days as $day) {
                $novena->days()->create($day);
            }

            return $novena->load('days');
        });

        return response()->json($novena, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $novena = $this->findModel($id, withTrashed: true);

        $data = Validator::make($request->all(), array_merge($this->updateRules($id), [
            'days' => ['nullable', 'array'],
            'days.*.day_number' => ['required_with:days', 'integer', 'min:1'],
            'days.*.title' => ['nullable', 'string', 'max:255'],
            'days.*.content' => ['required_with:days', 'string'],
            'days.*.prayer' => ['nullable', 'string'],
        ]))->validate();

        $days = $data['days'] ?? null;
        unset($data['days']);

        DB::transaction(function () use ($novena, $data, $days) {
            $novena->update($data);

            if (is_array($days)) {
                $novena->days()->forceDelete();

                foreach ($days as $day) {
                    $novena->days()->create($day);
                }
            }
        });

        return response()->json($novena->fresh()->load('days'));
    }

    protected function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:novenas,slug'],
            'category' => ['nullable', Rule::in(['regular', 'common'])],
            'patron_saint' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'opening_prayer' => ['nullable', 'string'],
            'closing_prayer' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('novenas', 'slug')->ignore($id)],
            'category' => ['nullable', Rule::in(['regular', 'common'])],
            'patron_saint' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'opening_prayer' => ['nullable', 'string'],
            'closing_prayer' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'is_active' => ['boolean'],
            'visit_count' => ['integer', 'min:0'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['slug'];
    }
}
