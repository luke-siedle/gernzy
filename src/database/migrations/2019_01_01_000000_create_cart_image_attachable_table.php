<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartImageAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_image_attachables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('image_id');
            $table->bigInteger('cart_image_attachable_id');
            $table->string('cart_image_attachable_type');
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
        Schema::dropIfExists('cart_categorizables');
    }
}
