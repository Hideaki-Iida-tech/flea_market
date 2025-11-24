<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // 支払い方法の定数（整数コード）を定義
    // 支払い方法を追加する場合はここをメンテナンス
    const PAYMENT_CONVENIENCE = 1; //コンビニ払い
    const PAYMENT_CARD = 2; // カード払い

    // それぞれの定数（整数コード）に対する表示ラベルを設定
    // セレクトボックス表示用
    // 支払い方法を追加する場合はここもメンテナンス
    public static $paymentLabels = [
        self::PAYMENT_CONVENIENCE => 'コンビニ払い',
        self::PAYMENT_CARD => 'カード支払い'
    ];

    // それぞれの定数（整数コード）に対するコード名を設定
    // Stripeで使用
    // 支払い方法を追加する場合はここもメンテナンス
    public static $paymentCodes = [
        self::PAYMENT_CONVENIENCE => 'konbini',
        self::PAYMENT_CARD => 'card',
    ];

    /**
     * 支払い方法コードに対応するラベル文字列（payment_method_label 仮想属性）を取得するアクセサ。
     *
     * payment_method の整数コードをもとに、静的配列 $paymentLabels から
     * 表示用ラベル（例：'カード払い'、'コンビニ払い'）を返却する。
     * 対応コードが存在しない場合は '不明' を返す。
     *
     * @return string  支払い方法の表示ラベル
     */
    public function getPaymentMethodLabelAttribute()
    {
        return self::$paymentLabels[$this->payment_method] ?? '不明';
    }

    /**
     * 支払い方法コードに対応する Stripe 用の決済コード
     * （payment_method_code 仮想属性）を取得するアクセサ。
     *
     * orders.payment_method に保存されている整数コードをもとに、
     * 静的配列 $paymentCodes から Stripe API で利用する決済種別
     * （例：'card' や 'konbini'）を返す。
     *
     * @return string|null  Stripe の決済コード。存在しない場合は null
     */
    public function getPaymentMethodCodeAttribute()
    {
        return self::$paymentCodes[$this->payment_method];
    }

    /**
     * 指定された item_id が orders テーブルに存在するかを判定する静的メソッド
     *
     * @param int $item_id
     * @return bool
     */
    public static function isSold(int $item_id): bool
    {
        return self::where('item_id', $item_id)->exists();
    }

    // カラムuser_id、item_id、price、address、payment_method、
    // postal_code、buildingを書き換え可能に設定
    protected $fillable = [
        'user_id',
        'item_id',
        'price',
        'address',
        'payment_method',
        'postal_code',
        'building'
    ];
}
