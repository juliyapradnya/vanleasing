<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRehiringOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rehiring_orders', function (Blueprint $table) {
            $table->id();
            $table->string('next_step');
            $table->integer('id_sales_order');
            //$table->integer('id_other_income');
            $table->string('new_sales_order_no');
            $table->integer('id_purchase_order');
            $table->date('vehicle_return_date');
            $table->double('sold_price');
            //$table->double('total_income');
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
        Schema::dropIfExists('rehiring_orders');
    }
}
