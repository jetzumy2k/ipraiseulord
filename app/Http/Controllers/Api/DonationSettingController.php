<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\DonationSetting;
use App\Services\DonationSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DonationSettingController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return DonationSetting::class;
    }

    protected function searchableFields(): array
    {
        return ['label', 'account_name', 'account_number', 'bank_name', 'paypal_email'];
    }

    protected function filterableFields(): array
    {
        return ['type', 'is_active'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'sort_order', 'label', 'created_at', 'updated_at'];
    }

    public function status(): JsonResponse
    {
        return response()->json(DonationSettingsService::statusSummary());
    }

    public function toggleGlobal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        DonationSettingsService::setGloballyEnabled($data['enabled']);

        return response()->json(DonationSettingsService::statusSummary());
    }

    public function bulkActive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DonationSetting::query()->update(['is_active' => $data['is_active']]);

        return response()->json(DonationSettingsService::statusSummary());
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeMultipartRequest($request);

        $data = Validator::make($request->all(), array_merge($this->storeRules(), [
            'qr_code' => ['nullable', 'image', 'max:2048'],
        ]))->validate();

        if ($request->hasFile('qr_code')) {
            $data['qr_code_path'] = $this->storeQrCode($request->file('qr_code'));
        }

        unset($data['qr_code']);

        $model = DonationSetting::create($data);
        $model->load($this->defaultRelations());

        return response()->json($model, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->findModel($id, withTrashed: true);

        $this->normalizeMultipartRequest($request);

        $data = Validator::make($request->all(), array_merge($this->updateRules($id), [
            'qr_code' => ['nullable', 'image', 'max:2048'],
        ]))->validate();

        if ($request->hasFile('qr_code')) {
            $this->deleteQrCode($model->qr_code_path);
            $data['qr_code_path'] = $this->storeQrCode($request->file('qr_code'));
        }

        unset($data['qr_code']);

        $model->update($data);
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    protected function storeRules(): array
    {
        return [
            'type' => ['required', Rule::in(['bank', 'paypal', 'ewallet'])],
            'label' => ['required', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'paypal_email' => ['nullable', 'email', 'max:255'],
            'ewallet_provider' => ['nullable', 'string', 'max:255'],
            'ewallet_number' => ['nullable', 'string', 'max:255'],
            'qr_code_path' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'display_locations' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'type' => ['sometimes', Rule::in(['bank', 'paypal', 'ewallet'])],
            'label' => ['sometimes', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'paypal_email' => ['nullable', 'email', 'max:255'],
            'ewallet_provider' => ['nullable', 'string', 'max:255'],
            'ewallet_number' => ['nullable', 'string', 'max:255'],
            'qr_code_path' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'display_locations' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    protected function normalizeMultipartRequest(Request $request): void
    {
        if ($request->has('display_locations') && is_string($request->input('display_locations'))) {
            $decoded = json_decode($request->input('display_locations'), true);

            if (is_array($decoded)) {
                $request->merge(['display_locations' => $decoded]);
            }
        }

        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    protected function storeQrCode($file): string
    {
        $path = $file->store('donations', 'public');

        return '/storage/'.$path;
    }

    protected function deleteQrCode(?string $path): void
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
