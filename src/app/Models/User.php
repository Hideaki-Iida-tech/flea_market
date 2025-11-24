<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
