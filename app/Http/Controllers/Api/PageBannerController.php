<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\PageBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PageBannerController extends Controller
{
    use HandlesCrud;

    public const BANNER_WIDTH = 1920;

    public const BANNER_HEIGHT = 360;

    protected function modelClass(): string
    {
        return PageBanner::class;
    }

    protected function searchableFields(): array
    {
        return ['route_name', 'label'];
    }

    protected function filterableFields(): array
    {
        return ['is_active'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'route_name', 'label', 'sort_order', 'created_at', 'updated_at'];
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeMultipartRequest($request);

        $data = Validator::make($request->all(), array_merge($this->storeRules(), [
            'banner_image' => ['nullable', 'image', 'max:5120'],
        ]))->validate();

        if ($request->hasFile('banner_image')) {
            $data['image_path'] = $this->storeBannerImage($request->file('banner_image'));
        }

        unset($data['banner_image']);

        $model = PageBanner::create($data);
        $model->load($this->defaultRelations());

        return response()->json($model, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->findModel($id, withTrashed: true);

        $this->normalizeMultipartRequest($request);

        $data = Validator::make($request->all(), array_merge($this->updateRules($id), [
            'banner_image' => ['nullable', 'image', 'max:5120'],
        ]))->validate();

        if ($request->hasFile('banner_image')) {
            $this->deleteBannerImage($model->image_path);
            $data['image_path'] = $this->storeBannerImage($request->file('banner_image'));
        }

        unset($data['banner_image']);

        $model->update($data);
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    protected function storeRules(): array
    {
        return [
            'route_name' => ['required', 'string', 'max:100', Rule::unique('page_banners', 'route_name')],
            'label' => ['required', 'string', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'overlay_opacity' => ['nullable', 'numeric', 'min:0.2', 'max:0.9'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'route_name' => ['sometimes', 'string', 'max:100', Rule::unique('page_banners', 'route_name')->ignore($id)],
            'label' => ['sometimes', 'string', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'overlay_opacity' => ['nullable', 'numeric', 'min:0.2', 'max:0.9'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    protected function normalizeMultipartRequest(Request $request): void
    {
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        if ($request->has('overlay_opacity') && $request->input('overlay_opacity') !== '') {
            $request->merge([
                'overlay_opacity' => (float) $request->input('overlay_opacity'),
            ]);
        }
    }

    protected function storeBannerImage($file): string
    {
        $path = $file->store('page-banners', 'public');

        return '/storage/'.$path;
    }

    protected function deleteBannerImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, '/storage/')) {
            return;
        }

        $relativePath = ltrim(str_replace('/storage/', '', $path), '/');

        if ($relativePath !== '') {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
