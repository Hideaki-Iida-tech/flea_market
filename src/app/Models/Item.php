<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // カラムitem_image、condition_id、item_name、brand、
    // description、price、user_idを書き換え可能に設定
    protected $fillable = [
        'item_image',
        'condition_id',
        'item_name',
        'brand',
        'description',
        'price',
        'user_id'
    ];
    // ータベースに存在しない “仮想カラム” を自動で生成
    protected $appends = ['image_url'];

    /**
     * 商品画像の完全URL（image_url 仮想属性）を取得するアクセサ。
     *
     * item_image が空の場合は空文字列を返し、外部URL（https:// で始まる）であれば
     * そのまま返す。ローカル保存の場合は storage ディレクトリのパスを元に
     * 公開URLを生成して返却する。
     *
     * @return string  商品画像の公開URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->item_image) return '';
        if (str_starts_with($this->item_image, 'https://')) return $this->item_image;
        return asset('storage/' . $this->item_image);
    }

    /**
     * この商品に紐づく注文情報を取得するリレーション。
     *
     * items.id を外部キー（item_id）として持つ Order モデルとの
     * 1 対 1（hasOne）リレーションを定義する。
     * 
     * 1つの商品は最大で1件の注文レコードのみを持つ仕様を想定。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }

    /**
     * この商品が持つ商品状態（コンディション）を取得するリレーション。
     *
     * items.condition_id を外部キーとして、Condition モデルに対する
     * belongsTo（多対一）リレーションを定義する。
     *
     * 1つの商品は必ず1つのコンディションに属する仕様を想定。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function condition()
    {
        return $this->belongsTo('\App\Models\Condition');
    }

    /**
     * この商品が属する複数のカテゴリを取得するリレーション。
     *
     * 中間テーブル category_item を用いる多対多（belongsToMany）リレーションを定義する。
     * 中間テーブルには created_at / updated_at のタイムスタンプが存在するため、
     * withTimestamps() により自動的に管理される。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('\App\Models\Category')->withTimeStamps();
    }

    /**
     * この商品を「いいね」したユーザーを取得するリレーション。
     *
     * 中間テーブル item_likes を利用した多対多（belongsToMany）リレーションで、
     * item_likes.user_id と item_likes.item_id により、ユーザーと商品の
     * いいね関係を管理している。withTimestamps() により、中間テーブルの
     * created_at / updated_at が自動的に管理される。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('\App\Models\User', 'item_likes')->withTimeStamps();
    }

    /**
     * 商品名に対する部分一致検索を行うローカルスコープ。
     *
     * キーワードが指定されている場合に、item_name カラムへ
     * LIKE 演算子による部分一致検索条件を付与する。
     * キーワードが空の場合は検索条件を追加しない。
     * ItemControllerのindexメソッド内でキーワードが入力されている場合に
     * コールされる
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKeywordSearch($query, $keyword)
    {

        if (!empty($keyword)) {
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }
    }
}
