<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIndexInProductRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_revenue', function (Blueprint $table) {
            $table->dropIndex('product_revenue_name_index');
            $table->dropIndex('product_revenue_product_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_revenue', function (Blueprint $table) {
            $table->index('name');
            $table->index('product_id');
        });
    }
}
