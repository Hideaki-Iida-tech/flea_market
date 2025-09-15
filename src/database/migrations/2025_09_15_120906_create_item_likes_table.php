<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // いいね機能を格納するitem_likesテーブル。itemsテーブルとusersテーブルの中間テーブル
        Schema::create('item_likes', function (Blueprint $table) {
            // usersテーブルのidを外部キーとするuser_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // itemsテーブルのidを外部キーとするitem_id
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->timestamps();
            // user_idとitem_idを主キーとする（重複防止）
            $table->primary(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_likes');
    }
}
