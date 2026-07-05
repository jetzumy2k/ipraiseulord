<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\StaticPage;
use App\Support\PageRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StaticPageController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return StaticPage::class;
    }

    protected function searchableFields(): array
    {
        return ['slug', 'page_route', 'title', 'content', 'meta_description'];
    }

    protected function filterableFields(): array
    {
        return ['is_published', 'page_route'];
    }

    protected function storeRules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:static_pages,slug'],
            'page_route' => ['nullable', 'string', 'max:100', Rule::in(array_keys(PageRoutes::ROUTES)), 'unique:static_pages,page_route'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'slug' => ['sometimes', 'string', 'max:255', 'unique:static_pages,slug,'.$id],
            'page_route' => ['nullable', 'string', 'max:100', Rule::in(array_keys(PageRoutes::ROUTES)), 'unique:static_pages,page_route,'.$id],
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }

    public function pageRoutes(): \Illuminate\Http\JsonResponse
    {
        return response()->json(PageRoutes::labels());
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizePageRoute($request);

        $data = Validator::make($request->all(), $this->storeRules())->validate();

        $model = StaticPage::create($data);
        $model->load($this->defaultRelations());

        return response()->json($model, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->normalizePageRoute($request);

        $model = $this->findModel($id, withTrashed: true);

        $data = Validator::make($request->all(), $this->updateRules($id))->validate();

        $model->update($data);
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    protected function normalizePageRoute(Request $request): void
    {
        if ($request->has('page_route') && $request->input('page_route') === '') {
            $request->merge(['page_route' => null]);
        }
    }

    protected function importUniqueKeys(): array
    {
        return ['slug'];
    }
}
