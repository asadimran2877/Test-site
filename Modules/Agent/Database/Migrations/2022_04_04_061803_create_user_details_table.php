<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('payout_verification_code', 6)->nullable();
            $table->string('verification_status')->comment('new, used, expired')->default('expired');
            $table->dateTime('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_details', 'payout_verification_code')) {
            Schema::table('user_details', function(Blueprint $table) {
                $table->dropColumn('payout_verification_code');
                $table->dropColumn('verification_status');
                $table->dropColumn('expires_at');
            });
        }
    }
}
