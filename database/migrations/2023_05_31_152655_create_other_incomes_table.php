<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_purchase_order');
            $table->date('date');
            $table->string('vehicle_registration_number');
            $table->string('description_income');
            $table->double('amount_oi');
            $table->string('payment_profile');
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
        Schema::dropIfExists('other_incomes');
    }
}
