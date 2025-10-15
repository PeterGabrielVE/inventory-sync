<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    public $timestamps = false; // created_at ya viene del SQL
    protected $fillable = [
        'product_id', 'old_stock', 'new_stock', 'delta', 'source', 'note', 'created_at'
    ];
}
