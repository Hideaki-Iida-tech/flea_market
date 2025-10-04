<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['item_image', 'condition_id', 'item_name', 'brand', 'description', 'price', 'user_id'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->item_image) return '';
        if (str_starts_with($this->item_image, 'https://')) return $this->item_image;
        return asset('storage/' . $this->item_image);
    }

    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
    public function condition()
    {
        return $this->belongsTo('\App\Models\Condition');
    }
    public function categories()
    {
        return $this->belongsToMany('\App\Models\Category')->withTimeStamps();
    }
    public function likes()
    {
        return $this->belongsToMany('\App\Models\User', 'item_likes')->withTimeStamps();
    }
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }
    }
}
