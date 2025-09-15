<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // categoriesテーブルとitemsテーブルを多対多とする中間テーブル        
        Schema::create('category_item', function (Blueprint $table) {
            // itemsテーブルのidを外部キーとするitem_id
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // categoriesテーブルのidを外部キーとするcategory_id
            $table->foreignId('category_id')->constrained('categories')->cascadeOndelete();
            $table->timestamps();
            // item_idとcategory_idを主キーとする（重複防止）
            $table->primary(['item_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_item');
    }
}
