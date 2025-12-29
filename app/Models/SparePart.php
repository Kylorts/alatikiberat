<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $fillable = [
        'part_number', 
        'name', 
        'category', 
        'brand', 
        'unit_price',
        'supplier_id' // Pastikan supplier_id diizinkan untuk diisi
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}