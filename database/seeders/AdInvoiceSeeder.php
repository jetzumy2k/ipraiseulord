<?php

namespace Database\Seeders;

use App\Models\AdInvoice;
use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $bookstore = Advertisement::query()->where('name', 'Catholic Bookstore Banner')->first();
        $retreat = Advertisement::query()->where('name', 'Parish Retreat Promotion')->first();

        if (! $bookstore || ! $retreat) {
            return;
        }

        $invoices = [
            [
                'advertisement_id' => $bookstore->id,
                'invoice_number' => 'INV-2026-001',
                'client_name' => 'Holy Family Catholic Bookstore',
                'client_email' => 'billing@hfcbookstore.example',
                'amount' => 500.00,
                'status' => 'paid',
                'invoice_date' => now()->startOfYear()->toDateString(),
                'due_date' => now()->startOfYear()->addDays(30)->toDateString(),
                'paid_date' => now()->startOfYear()->addDays(14)->toDateString(),
                'notes' => 'Annual banner and sidebar placement for 2026.',
            ],
            [
                'advertisement_id' => $bookstore->id,
                'invoice_number' => 'INV-2026-002',
                'client_name' => 'Holy Family Catholic Bookstore',
                'client_email' => 'billing@hfcbookstore.example',
                'amount' => 125.00,
                'status' => 'sent',
                'invoice_date' => now()->subMonths(1)->toDateString(),
                'due_date' => now()->addDays(15)->toDateString(),
                'paid_date' => null,
                'notes' => 'Mid-year sidebar renewal.',
            ],
            [
                'advertisement_id' => $retreat->id,
                'invoice_number' => 'INV-2026-003',
                'client_name' => 'St. Michael Parish',
                'client_email' => 'office@stmichael.example',
                'amount' => 250.00,
                'status' => 'paid',
                'invoice_date' => now()->subMonths(2)->toDateString(),
                'due_date' => now()->subMonths(2)->addDays(30)->toDateString(),
                'paid_date' => now()->subMonths(2)->addDays(7)->toDateString(),
                'notes' => 'Parish retreat promotion — sidebar and footer embed.',
            ],
            [
                'advertisement_id' => $retreat->id,
                'invoice_number' => 'INV-2026-004',
                'client_name' => 'St. Michael Parish',
                'client_email' => 'office@stmichael.example',
                'amount' => 250.00,
                'status' => 'draft',
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'paid_date' => null,
                'notes' => 'Draft invoice for Q3 retreat campaign extension.',
            ],
        ];

        foreach ($invoices as $invoice) {
            AdInvoice::updateOrCreate(
                ['invoice_number' => $invoice['invoice_number']],
                $invoice
            );
        }
    }
}
