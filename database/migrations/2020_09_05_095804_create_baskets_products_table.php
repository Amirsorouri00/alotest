<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasketsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baskets_products', function (Blueprint $table) {
            $table->unsignedInteger('basket_id');
            $table->unsignedInteger('product_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('basket_id')->references('id')->on('baskets')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['user_id','role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baskets_products');
    }
}
