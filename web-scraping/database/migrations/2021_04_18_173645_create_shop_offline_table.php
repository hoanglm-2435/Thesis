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
            $table->string('name');
            $table->double('rating');
            $table->string('location');
            $table->string('phone_number');
            $table->timestamps();

            $table->unique(['name', 'location']);
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
