<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\AdInvoice;
use Illuminate\Validation\Rule;

class AdInvoiceController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return AdInvoice::class;
    }

    protected function defaultRelations(): array
    {
        return ['advertisement'];
    }

    protected function searchableFields(): array
    {
        return ['invoice_number', 'client_name', 'client_email', 'notes'];
    }

    protected function filterableFields(): array
    {
        return ['advertisement_id', 'status'];
    }

    protected function sortableFields(): array
    {
        return ['id', 'invoice_date', 'due_date', 'amount', 'status', 'created_at', 'updated_at'];
    }

    protected function storeRules(): array
    {
        return [
            'advertisement_id' => ['required', 'exists:advertisements,id'],
            'invoice_number' => ['required', 'string', 'max:255', 'unique:ad_invoices,invoice_number'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['draft', 'sent', 'paid', 'cancelled'])],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'advertisement_id' => ['sometimes', 'exists:advertisements,id'],
            'invoice_number' => ['sometimes', 'string', 'max:255', Rule::unique('ad_invoices', 'invoice_number')->ignore($id)],
            'client_name' => ['sometimes', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['draft', 'sent', 'paid', 'cancelled'])],
            'invoice_date' => ['sometimes', 'date'],
            'due_date' => ['nullable', 'date'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function importUniqueKeys(): array
    {
        return ['invoice_number'];
    }
}
