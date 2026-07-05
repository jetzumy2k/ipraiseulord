<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\SocialMediaSetting;

class SocialMediaSettingController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return SocialMediaSetting::class;
    }

    protected function searchableFields(): array
    {
        return ['platform', 'url', 'handle'];
    }

    protected function filterableFields(): array
    {
        return ['platform', 'is_active'];
    }

    protected function storeRules(): array
    {
        return [
            'platform' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255'],
            'display_locations' => ['nullable', 'array'],
            'show_share_buttons' => ['boolean'],
            'show_follow_links' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'platform' => ['sometimes', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255'],
            'display_locations' => ['nullable', 'array'],
            'show_share_buttons' => ['boolean'],
            'show_follow_links' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
