<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'advertisement_id',
    'invoice_number',
    'client_name',
    'client_email',
    'amount',
    'status',
    'invoice_date',
    'due_date',
    'paid_date',
    'notes',
])]
class AdInvoice extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'invoice_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }
}
