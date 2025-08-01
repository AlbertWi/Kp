<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'product_id', 'imei', 'price'];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function inventoryItem()
    {
        return $this->belongsTo(\App\Models\InventoryItem::class, 'imei', 'imei');
    }

}
