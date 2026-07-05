<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoSettingsController extends Controller
{
    public function __construct(
        protected SeoService $seo,
    ) {
    }

    public function show(): JsonResponse
    {
        return response()->json($this->seo->adminSettings());
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'home_headline' => ['nullable', 'string', 'max:255'],
            'default_description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'twitter_handle' => ['nullable', 'string', 'max:100'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'og_image_file' => ['nullable', 'image', 'max:5120'],
            'page_meta' => ['nullable', 'array'],
            'page_meta.*.title' => ['nullable', 'string', 'max:255'],
            'page_meta.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('og_image_file')) {
            $existing = $this->seo->setting(SeoService::SETTING_OG_IMAGE);
            $this->deleteStoredImage($existing);

            $path = $request->file('og_image_file')->store('seo', 'public');
            $data['og_image'] = '/storage/'.$path;
        }

        unset($data['og_image_file']);

        $this->seo->updateAdminSettings($data);

        return response()->json([
            'message' => 'SEO settings saved.',
            'settings' => $this->seo->adminSettings(),
        ]);
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, '/storage/')) {
            return;
        }

        $relativePath = ltrim(str_replace('/storage/', '', $path), '/');

        if ($relativePath !== '' && $relativePath !== 'seo/og-default.png') {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
