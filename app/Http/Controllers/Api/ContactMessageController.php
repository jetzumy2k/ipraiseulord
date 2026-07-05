<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\ContactMessage;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    use HandlesCrud;

    protected function modelClass(): string
    {
        return ContactMessage::class;
    }

    protected function searchableFields(): array
    {
        return ['name', 'email', 'subject', 'message'];
    }

    protected function filterableFields(): array
    {
        return ['status'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['nullable', Rule::in(['new', 'read', 'replied', 'archived'])],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['sometimes', 'string'],
            'status' => ['nullable', Rule::in(['new', 'read', 'replied', 'archived'])],
        ];
    }
}
