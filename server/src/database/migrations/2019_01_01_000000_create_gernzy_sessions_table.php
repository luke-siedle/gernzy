<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGernzySessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gernzy_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 80)
                ->unique()
                ->nullable()
                ->default(null);
            $table->json('data')->nullable();
            $table->bigInteger('cart_id')->nullable();
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('gernzy_sessions');
    }
}
