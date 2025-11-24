<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condition;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 出品されている商品を投入するseeder
     * 商品画像としてawsのs3に保存されている画像の外部URLを設定している
     * 
     * @return void
     */
    public function run()
    {
        $conditionId = Condition::where('name', '良好')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー1')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition_id' => $conditionId, // 良好
            'item_name' => '腕時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '目立った傷や汚れなし')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー2')->first()->id
            ?? User::inRandomOrder()->first()->id;
        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'condition_id' => $conditionId, // 目立った傷や汚れなし
            'item_name' => 'HDD',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', 'やや傷や汚れあり')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー3')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'condition_id' => $conditionId, // やや傷や汚れあり
            'item_name' => '玉ねぎ3束',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'price' => 300,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '状態が悪い')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー1')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'condition_id' => $conditionId, // 状態が悪い
            'item_name' => '革靴',
            'brand' => null,
            'description' => 'クラッシックなデザインの革靴',
            'price' => 4000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '良好')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー2')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'condition_id' => $conditionId, // 良好
            'item_name' => 'ノートPC',
            'brand' => null,
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '目立った傷や汚れなし')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー3')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'condition_id' => $conditionId, // 目立った傷や汚れなし
            'item_name' => 'マイク',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'price' => 8000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', 'やや傷や汚れあり')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー1')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'condition_id' => $conditionId, // やや傷や汚れあり
            'item_name' => 'ショルダーバッグ',
            'brand' => null,
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '状態が悪い')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー2')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'condition_id' => $conditionId, // 状態が悪い
            'item_name' => 'タンブラー',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '良好')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー3')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition_id' => $conditionId, // 良好
            'item_name' => 'コーヒーミル',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $conditionId = Condition::where('name', '目立った傷や汚れなし')->value('id')
            ?? Condition::inRandomOrder()->first()->id;
        $userId = User::where('name', 'テストユーザー1')->first()->id
            ?? User::inRandomOrder()->first()->id;

        $param = [
            'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'condition_id' => $conditionId, // 目立った傷や汚れなし
            'item_name' => 'メイクセット',
            'brand' => null,
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'user_id' => $userId, // UsersTableSeederで作成したユーザーid
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);
    }
}
