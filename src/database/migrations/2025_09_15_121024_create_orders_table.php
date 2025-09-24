<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ordersテーブル。注文情報を格納
        Schema::create('orders', function (Blueprint $table) {
            // 主キー
            $table->id();
            // usersテーブルのidを外部キーとするuser_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // itemsテーブルのidを外部キーとするitem_idユニーク指定
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete()->unique();
            // 注文時の値段を格納
            $table->unsignedBigInteger('price');
            // 商品送付先
            $table->string('address', 255);
            // 支払い方法のコード
            $table->unsignedTinyInteger('payment_method');
            // 商品送付先の郵便番号
            $table->string('postal_code', 8);
            // 商品送付先の建物名
            $table->string('building', 255)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
