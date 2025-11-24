<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 商品データのカテゴリを示すcategory_itemテーブルのseeder
     * 
     * @return void
     */
    public function run()
    {
        // 腕時計→ファッション
        $itemId = Item::where('item_name', '腕時計')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'ファッション')->value('id')
            ?? Category::all()->random()->id;

        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 腕時計→メンズ
        $itemId = Item::where('item_name', '腕時計')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'メンズ')->value('id')
            ?? Category::all()->random()->id;

        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 腕時計→アクセサリ
        $itemId = Item::where('item_name', '腕時計')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'アクセサリー')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // HDD→家電
        $itemId = Item::where('item_name', 'HDD')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', '家電')->value('id')
            ?? Category::all()->random()->id;

        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 玉ねぎ3束→キッチン
        $itemId = Item::where('item_name', '玉ねぎ3束')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'キッチン')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 玉ねぎ3束→ハンドメイド
        $itemId = Item::where('item_name', '玉ねぎ3束')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'ハンドメイド')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 革靴→ファッション
        $itemId = Item::where('item_name', '革靴')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'ファッション')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // 革靴→メンズ
        $itemId = Item::where('item_name', '革靴')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'メンズ')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // ノートPC→家電
        $itemId = Item::where('item_name', 'ノートPC')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', '家電')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // マイク→家電
        $itemId = Item::where('item_name', 'マイク')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', '家電')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // ショルダーバック→ファッション
        $itemId = Item::where('item_name', 'ショルダーバッグ')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'ファッション')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // ショルダーバック→レディース
        $itemId = Item::where('item_name', 'ショルダーバッグ')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'レディース')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // タンブラー→キッチン
        $itemId = Item::where('item_name', 'タンブラー')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'キッチン')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // コーヒーミル→インテリア
        $itemId = Item::where('item_name', 'コーヒーミル')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'インテリア')->value('id')
            ?? Category::all()->random()->id;

        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // コーヒーミル→キッチン
        $itemId = Item::where('item_name', 'コーヒーミル')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'キッチン')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // メイクセット→レディース
        $itemId = Item::where('item_name', 'メイクセット')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'レディース')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);

        // メイクセット→コスメ
        $itemId = Item::where('item_name', 'メイクセット')->value('id')
            ?? Item::all()->random()->id;
        $categoryId = Category::where('name', 'コスメ')->value('id')
            ?? Category::all()->random()->id;
        $param = [
            'item_id' => $itemId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('category_item')->insert($param);
    }
}
