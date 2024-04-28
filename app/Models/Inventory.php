<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'amount',
        'price',
        'product_id'
    ];

    public function product(): BelongsTo
{
    return $this->belongsTo(Product::class, 'foreign_key');
}
}
