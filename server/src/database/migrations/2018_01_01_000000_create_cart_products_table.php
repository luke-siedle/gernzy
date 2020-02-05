<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_products', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            ;
            $table->bigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('price_cents')->bigInteger()->nullable();
            $table->string('price_currency')->string()->nullable();
            $table->string('short_description')->text()->nullable();
            $table->string('long_description')->text()->nullable();
            $table->string('status');
            $table->tinyInteger('published')->default(0);
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
        Schema::dropIfExists('cart_products');
    }
}
