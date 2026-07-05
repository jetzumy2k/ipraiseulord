<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\Advertisement;
use App\Models\AdInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdvertisementController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return Advertisement::class;
    }

    protected function defaultRelations(): array
    {
        return ['invoices'];
    }

    protected function searchableFields(): array
    {
        return ['name', 'url'];
    }

    protected function filterableFields(): array
    {
        return ['type', 'is_active'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['embed', 'manual'])],
            'embed_script' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'placements' => ['nullable', 'array'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'impression_count' => ['integer', 'min:0'],
            'click_count' => ['integer', 'min:0'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['embed', 'manual'])],
            'embed_script' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'placements' => ['nullable', 'array'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'impression_count' => ['integer', 'min:0'],
            'click_count' => ['integer', 'min:0'],
        ];
    }

    public function salesReport(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $adsQuery = Advertisement::query();
        $invoiceQuery = AdInvoice::query()->with('advertisement');

        if ($from) {
            $adsQuery->whereDate('start_date', '>=', $from);
            $invoiceQuery->whereDate('invoice_date', '>=', $from);
        }

        if ($to) {
            $adsQuery->whereDate('end_date', '<=', $to);
            $invoiceQuery->whereDate('invoice_date', '<=', $to);
        }

        $totalRevenue = (clone $invoiceQuery)->where('status', 'paid')->sum('amount');
        $pendingRevenue = (clone $invoiceQuery)->whereIn('status', ['draft', 'sent'])->sum('amount');

        return response()->json([
            'summary' => [
                'total_advertisements' => $adsQuery->count(),
                'active_advertisements' => (clone $adsQuery)->where('is_active', true)->count(),
                'total_impressions' => (clone $adsQuery)->sum('impression_count'),
                'total_clicks' => (clone $adsQuery)->sum('click_count'),
                'total_revenue' => $totalRevenue,
                'pending_revenue' => $pendingRevenue,
            ],
            'invoices' => $invoiceQuery->orderByDesc('invoice_date')->get(),
            'advertisements' => $adsQuery->with('invoices')->orderByDesc('created_at')->get(),
        ]);
    }
}
