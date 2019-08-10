<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->nullable();
            $table->bigInteger('user_id')->nullable();

            $table->smallInteger('currency_id')->nullable();
            $table->string('status')->nullable();

            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();

            $table->string('shipping_address_line_1')->nullable();
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_address_postcode')->nullable();
            $table->string('shipping_address_state')->nullable();
            $table->string('shipping_address_country')->nullable();

            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_address_postcode')->nullable();
            $table->string('billing_address_state')->nullable();
            $table->string('billing_address_country')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('agree_to_terms')->tinyInteger();
            $table->string('notes')->string();

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
        Schema::dropIfExists('cart_orders');
    }
}
