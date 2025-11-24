<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // itemsテーブル
        Schema::create('items', function (Blueprint $table) {
            // 主キー
            $table->id();
            // 商品画像の相対パス、またはseederで生成する外部URL
            $table->string('item_image', 255);
            // conditionsテーブルのidを外部キーとするcondition_id
            $table->foreignId('condition_id')->constrained('conditions')->cascadeOnDelete();
            // 商品名
            $table->string('item_name', 255);
            // ブランド名
            $table->string('brand', 255)->nullable();
            // 商品の説明
            $table->string('description', 255);
            // 値段
            $table->unsignedBigInteger('price');
            // usersテーブルのidを外部キーとするuser_id（出品者）
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
