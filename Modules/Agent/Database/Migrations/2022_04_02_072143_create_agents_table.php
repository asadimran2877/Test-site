<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('defaultCountry', 4)->nullable()->default(null);
            $table->string('phone', 20)->unique()->nullable()->default(null);
            $table->string('carrierCode', 6)->nullable()->default(null);
            $table->string('formattedPhone', 30)->nullable()->default(null);
            $table->string('picture', 100)->nullable()->default(null);
            $table->string('type', 30)->default('Agent');
            $table->string('parent_id', 11)->nullable()->default(null);
            $table->string('status', 11)->default('Active')->comment('Active, Inactive, Suspended');
            $table->string('remember_token', 100)->nullable()->default(null);
            
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
        Schema::dropIfExists('agents');
    }
}
