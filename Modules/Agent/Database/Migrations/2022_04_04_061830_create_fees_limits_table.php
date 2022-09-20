<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeesLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees_limits', function (Blueprint $table) {
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
        if (Schema::hasColumn('fees_limits', 'agent_percentage')) {
            Schema::table('fees_limits', function(Blueprint $table) {
                $table->dropColumn('agent_percentage');
            });
        }
    }
}
