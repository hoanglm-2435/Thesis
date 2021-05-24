<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOfflineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_offline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('place_id');
            $table->string('name');
            $table->string('city');
            $table->string('address');
            $table->string('phone_number');
            $table->double('rating');
            $table->integer('user_rating');

            $table->unique(['place_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_offline');
    }
}
