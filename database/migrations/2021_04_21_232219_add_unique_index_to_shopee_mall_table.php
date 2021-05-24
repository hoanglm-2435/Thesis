<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToShopeeMallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopee_mall', function (Blueprint $table) {
            $table->unique(['cate_id', 'url']);
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
            $table->dropUnique('shopee_mall_cate_id_url_unique');
        });
    }
}
