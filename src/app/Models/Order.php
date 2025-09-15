<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const PAYMENT_CONVENIENCE = 1;
    const PAYMENT_CARD = 2;

    public static $paymentLabels = [
        self::PAYMENT_CONVENIENCE => 'コンビニ払い',
        self::PAYMENT_CARD => 'カード支払い'
    ];
    public function getPaymentMethodLabelAttribute()
    {
        return self::$paymentLabels[$this->payment_method] ?? '不明';
    }

    protected $fillable = ['user_id', 'item_id', 'price', 'address', 'payment_method', 'postal_code', 'building'];
}
