<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountStockDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('count_stock_details', function (Blueprint $table) {
            $table->id();

            $table->integer('count_stock_id')->index('count_stock_details_count_stock_id');
            $table->integer('product_id')->index('count_stock_details_product_id');

            $table->integer('old_stock');
            $table->integer('new_stock');

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
        Schema::dropIfExists('count_stock_details');
    }
}
