<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // 認証データのシーダー
        $this->call(UsersTableSeeder::class);
        // 商品の状態のシーダー
        $this->call(ConditionsTableSeeder::class);
        // カテゴリーのシーダー
        $this->call(CategoriesTableSeeder::class);
        // 商品詳細情報のシーダー
        // 必ず前4つのシーダーの後にコール
        $this->call(ItemsTableSeeder::class);
        // itemsテーブルに登録した商品のカテゴリーを表す中間テーブルのシーダーをコール
        $this->call(CategoryItemTableSeeder::class);
    }
}
