<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamagedStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_no',
        'product_id',
        'warehouse_id',
        'quantity',
        'date',
        'reason',
        'created_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
