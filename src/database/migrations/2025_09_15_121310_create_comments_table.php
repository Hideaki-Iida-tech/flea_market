<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // コメント情報を格納
        Schema::create('comments', function (Blueprint $table) {
            // 主キー
            $table->id();
            // usersテーブルのidを外部キーとするuser_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // itemsテーブルのidを外部キーとするitem_id
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // コメントの本文
            $table->string('body', 255);
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
        Schema::dropIfExists('comments');
    }
}
