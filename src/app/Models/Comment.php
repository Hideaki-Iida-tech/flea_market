<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // カラムuser_id、item_id、bodyを書き換え可能に設定
    protected $fillable = ['user_id', 'item_id', 'body'];

    /**
     * コメントを投稿したユーザーを取得するリレーション。
     *
     * comments.user_id を外部キーとして、User モデルに対する
     * belongsTo（多対一）リレーションを定義する。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
