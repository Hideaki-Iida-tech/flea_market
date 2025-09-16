<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['item_image', 'condition_id', 'item_name', 'brand', 'description', 'price', 'user_id'];

    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
    public function condition()
    {
        return $this->hasOne('\App\Models\Condition');
    }
}
