<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cgy_id');  //商品分類
            $table->string('title',50);
            $table->string('pics')->nullable();
            $table->integer('price_og')->default(0); //初始售價
            $table->integer('price_new')->nullable(); //更新售價
            $table->string('badge',30)->nullable(); //小標
            $table->integer('star')->default(10); //星數，5星為10
            $table->integer('stock')->default(0); //庫存數量
            $table->string('desc',400)->nullable(); //商品描述
            $table->string('chars',300)->nullable(); //商品特色
            $table->string('sku',100)->nullable(); //sku編號
            $table->string('options',500)->nullable(); //可供選項
            $table->integer('sort')->default(0); //排序
            $table->boolean('enabled')->default(true);
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
