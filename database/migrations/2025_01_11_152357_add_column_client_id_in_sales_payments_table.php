<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnClientIdInSalesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_sales', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->nullable()->index('client_id_payment_sale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sales', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
}
