<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemittancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remittances', function (Blueprint $table) {
            
            $table->increments('id');

            $table->integer('sender_id')->unsigned()->index();
            $table->foreign('sender_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('recipent_detail_id')->unsigned()->index();
            $table->foreign('recipent_detail_id')->references('id')->on('recipient_details')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('transferred_currency_id')->unsigned()->index();

            $table->integer('received_currency_id')->unsigned()->index();

            $table->integer('remittance_payout_method_id')->unsigned()->index();

            $table->integer('beneficiary_detail_id')->unsigned()->index()->comment('ex. recipient bank details or mobile money');

            $table->integer('payment_method_id')->unsigned()->index();

            $table->decimal('transferred_amount');

            $table->decimal('received_amount');

            $table->decimal('fees');

            $table->decimal('total');

            $table->decimal('exchange_rate');

            $table->string('reference');

            $table->string('uuid');

            $table->text('status');
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
        Schema::dropIfExists('remittances');
    }
}
