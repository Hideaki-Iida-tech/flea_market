<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * users1テーブルのseeder
     * テストユーザー1、テストユーザー2、テストユーザー3の3名を投入
     * 
     * @return void
     */
    public function run()
    {
        // ユーザー情報のダミーデータを作成
        $param = [
            'name' => 'テストユーザー1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'is_profile_completed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'テストユーザー2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'is_profile_completed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'テストユーザー3',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'is_profile_completed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('users')->insert($param);
    }
}
