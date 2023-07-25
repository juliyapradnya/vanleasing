<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sales_order');
            $table->string('purchase_method');
            $table->string('vehicle_registration');
            $table->string('hp_finance_provider');
            $table->date('hire_purchase_starting_date');
            $table->double('hp_interest_per_annum');
            $table->double('hp_deposit_amount');
            $table->integer('hp_term');
            $table->double('documentation_fees_pu');
            $table->double('final_fees');
            $table->double('other_fees');
            $table->double('price_otr');
            $table->double('monthly_payment');
            $table->double('final_payment');
            $table->string('hp_interest_type');
            $table->double('financing_amount');
            $table->double('regular_monthly_payment');
            $table->string('status_next_step');
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
        Schema::dropIfExists('purchase_orders');
    }
}
