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
     * @return void
     */
    public function run()
    {
        //
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
