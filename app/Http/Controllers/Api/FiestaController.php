<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\Fiesta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FiestaController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return Fiesta::class;
    }

    protected function searchableFields(): array
    {
        return ['title', 'honoree_name', 'description', 'liturgical_rank'];
    }

    protected function filterableFields(): array
    {
        return ['category', 'liturgical_rank', 'is_active', 'is_movable', 'month'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'title', 'month', 'day', 'category', 'sort_order', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return $this->validationRules();
    }

    protected function updateRules(int $id): array
    {
        return $this->validationRules($id);
    }

    protected function importUniqueKeys(): array
    {
        return ['title', 'month', 'day'];
    }

    public function store(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), $this->storeRules())->validate();
        $model = Fiesta::create($this->normalizePayload($data));
        $model->load($this->defaultRelations());

        return response()->json($model, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->findModel($id, withTrashed: true);
        $data = Validator::make($request->all(), $this->updateRules($id))->validate();
        $model->update($this->normalizePayload($data));
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validationRules(?int $id = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in([
                Fiesta::CATEGORY_CHRIST,
                Fiesta::CATEGORY_FATHER,
                Fiesta::CATEGORY_MARY,
                Fiesta::CATEGORY_SAINT,
                Fiesta::CATEGORY_ANGEL,
            ])],
            'honoree_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'liturgical_rank' => ['nullable', 'string', 'max:255'],
            'is_movable' => ['boolean'],
            'movable_rule' => [
                Rule::requiredIf(fn () => request()->boolean('is_movable')),
                'nullable',
                'string',
                'max:255',
            ],
            'feast_date' => [
                Rule::requiredIf(fn () => ! request()->boolean('is_movable')),
                'nullable',
                'date',
            ],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizePayload(array $data): array
    {
        if (! empty($data['feast_date'])) {
            $date = strtotime((string) $data['feast_date']);

            if ($date !== false) {
                $data['month'] = (int) date('n', $date);
                $data['day'] = (int) date('j', $date);
            }
        }

        unset($data['feast_date']);

        if (! empty($data['is_movable'])) {
            $data['month'] = null;
            $data['day'] = null;
        } else {
            $data['movable_rule'] = null;
        }

        return $data;
    }
}
