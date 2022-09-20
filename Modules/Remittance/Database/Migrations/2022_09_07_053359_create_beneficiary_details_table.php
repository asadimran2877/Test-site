<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeneficiaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_details', function (Blueprint $table) {

            $table->increments('id')->unsigned();
            
            $table->string('monilemoney_network')->nullable();
            $table->string('mobilemoney_number')->nullable();

            $table->string('bank_name')->nullable();

            $table->string('account_name')->nullable();

            $table->string('account_number')->nullable();

            $table->string('swift_code')->nullable();

            $table->string('branch_name')->nullable();

            $table->string('branch_city')->nullable();

            $table->string('branch_address')->nullable();

            $table->string('country')->nullable();

            $table->integer('is_default')->default('0');
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
        Schema::dropIfExists('beneficiary_details');
    }
}
