<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'business_name',
        'balance',
    ];
}
