<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function(Blueprint $table) {
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
        if (Schema::hasColumn('transactions', 'agent_id')) {
            Schema::table('transactions', function(Blueprint $table) {
                $table->dropColumn(['agent_id', 'agent_percentage']);
            });
        }
    }
}
