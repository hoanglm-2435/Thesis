<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_revenue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id')->index();
            $table->bigInteger('product_id')->index();
            $table->bigInteger('cate_id')->index();
            $table->string('name')->index();
            $table->bigInteger('price');
            $table->bigInteger('sold_per_day');
            $table->bigInteger('revenue_per_day');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_revenue');
    }
}
