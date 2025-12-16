<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_no',
        'category_id',
        'warehouse_id',
        'amount',
        'date',
        'notes',
        'created_by'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
