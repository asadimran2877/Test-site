<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_details', function (Blueprint $table) {

            $table->increments('id')->unsigned();

            $table->string('first_name')->index();
            $table->string('last_name')->index();

            $table->string('mobile_number')->index();

            $table->string('email')->nullable();

            $table->string('nick_name');

            $table->string('city');

            $table->string('street');

            $table->string('country')->index();
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
        Schema::dropIfExists('recipient_details');
    }
}
