<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 商品状態を格納するconditionsテーブルのマスタデータを投入するseeder
     * このseederが実行される前に、items/create.blade.phpのviewが表示される場合には、
     * config/master.phpからマスターデータを読み込んで、マスタデータとしてDBに保存
     * 
     * @return void
     */
    public function run()
    {
        $param = ['name' => '良好', 'created_at' => now(), 'updated_at' => now(),];
        DB::table('conditions')->insert($param);

        $param = ['name' => '目立った傷や汚れなし', 'created_at' => now(), 'updated_at' => now(),];
        DB::table('conditions')->insert($param);

        $param = ['name' => 'やや傷や汚れあり', 'created_at' => now(), 'updated_at' => now(),];
        DB::table('conditions')->insert($param);

        $param = ['name' => '状態が悪い', 'created_at' => now(), 'updated_at' => now(),];
        DB::table('conditions')->insert($param);
    }
}
