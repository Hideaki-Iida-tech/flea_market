<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User モデル
 *
 * このモデルは Laravel のメールアドレス確認機能を有効にするため、
 * Illuminate\Contracts\Auth\MustVerifyEmail インターフェイスを実装しています。
 *
 * MustVerifyEmail を実装することで、ユーザー登録後にメールアドレスの検証が
 * 必須となり、未確認ユーザーはメール認証が完了するまで特定の機能に
 * アクセスできないようフレームワーク側が自動的に制御します。
 *
 * また、メール認証通知の送信、認証状態の判定、認証済みユーザーのみが
 * 利用できるルート保護などが、Laravel 標準の仕組みによって提供されます。
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, bool>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image',
        'is_profile_completed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 指定したユーザーのプロフィールが完了状態かどうかを判定する静的メソッド。
     *
     * users.id に一致するユーザーを取得し、レコードが存在する場合は
     * is_profile_completed フラグの値を返す。ユーザーが存在しない場合は
     * false を返す。
     *
     * @param  int  $user_id  判定対象のユーザーID
     * @return bool           プロフィールが完了していれば true、未完了または未登録なら false
     */
    public static function isProfileCompleted(int $user_id): bool
    {
        $user = Self::where('id', $user_id)->first();
        return $user?->is_profile_completed ?? false;
    }
}
