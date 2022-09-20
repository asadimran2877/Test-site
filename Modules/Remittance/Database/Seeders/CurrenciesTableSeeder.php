<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Schema::table('currencies', function(\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('remittance_type')->nullable()->comment('send / receive/ send,receive')->after('status');
            $table->string('remittance_payout_method_id')->nullable()->after('remittance_type');
        });
    }
}
