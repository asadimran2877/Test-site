<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->integer('agent_id')->unsigned()->index()->nullable();
            $table->decimal('agent_percentage', 20, 8)->nullable()->default(0.00000000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('deposits', 'agent_percentage')) {
            Schema::table('deposits', function(Blueprint $table) {
                $table->dropColumn(['agent_id', 'agent_percentage']);
            });
        }
    }
}
