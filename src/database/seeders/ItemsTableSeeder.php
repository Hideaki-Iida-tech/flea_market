<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $param = [
            'item_image' => '',
            'condition_id' => 1, // 良好
            'item_name' => '腕時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 2, // 目立った傷や汚れなし
            'item_name' => 'HDD',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 3, // やや傷や汚れあり
            'item_name' => '玉ねぎ3束',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'price' => 300,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 4, // 状態が悪い
            'item_name' => '革靴',
            'brand' => null,
            'description' => 'クラッシックなデザインの革靴',
            'price' => 4000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 1, // 良好
            'item_name' => 'ノートPC',
            'brand' => null,
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 2, // 目立った傷や汚れなし
            'item_name' => 'マイク',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'price' => 8000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 3, // やや傷や汚れあり
            'item_name' => 'ショルダーバッグ',
            'brand' => null,
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 4, // 状態が悪い
            'item_name' => 'タンブラー',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 1, // 良好
            'item_name' => 'コーヒーミル',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_image' => '',
            'condition_id' => 2, // 目立った傷や汚れなし
            'item_name' => 'メイクセット',
            'brand' => null,
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'user_id' => 1, // UsersTableSeederで作成したユーザーid
        ];
        DB::table('items')->insert($param);
    }
}
