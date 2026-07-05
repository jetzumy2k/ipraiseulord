<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\SystemSetting;

class SystemSettingController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return SystemSetting::class;
    }

    protected function searchableFields(): array
    {
        return ['key', 'value', 'group'];
    }

    protected function filterableFields(): array
    {
        return ['group'];
    }

    protected function storeRules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:system_settings,key'],
            'value' => ['nullable', 'string'],
            'group' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'key' => ['sometimes', 'string', 'max:255', 'unique:system_settings,key,'.$id],
            'value' => ['nullable', 'string'],
            'group' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['key'];
    }
}
