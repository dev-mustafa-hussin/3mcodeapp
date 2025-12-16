<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'date',
        'subtotal',
        'tax_total',
        'shipping_cost',
        'discount_total',
        'grand_total',
        'paid_amount',
        'due_amount',
        'status',
        'payment_status',
        'notes',
        'warehouse_id',
        'created_by'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
