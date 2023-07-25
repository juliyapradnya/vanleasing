<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('id_purchase_order');
            $table->string('type');
            $table->string('agreement_no');
            $table->string('cust_name');
            $table->string('sales_person');
            $table->date('contract_start_date');
            $table->string('vehicle_manufacturer');
            $table->string('vehicle_model');
            $table->string('vehicle_variant');
            $table->double('basic_list_price');
            $table->double('residual_value');
            $table->double('annual_mileage');
            $table->integer('term_months');
            $table->double('initial_rental');
            $table->double('documentation_fees');
            $table->double('monthly_rental');
            $table->double('other_income');
            $table->integer('margin_term');
            $table->double('total_income');
            $table->double('next_step_status_sales');
            
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
        Schema::dropIfExists('sales_orders');
    }
}
