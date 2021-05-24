<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateShopeeMallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopee_mall', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->integer('shop_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopee_mall', function (Blueprint $table) {
            $table->string('url');
            $table->dropColumn('shop_id');
        });
    }
}
