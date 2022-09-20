<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Schema::table('countries', function(\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('currency_code')->nullable()->after('phone_code');
           
        });

        DB::table('countries')->where('id', '226')->where('short_name', 'US')->update(['currency_code' => 'USD']);
        DB::table('countries')->where('id', '225')->where('short_name', 'GB')->update(['currency_code' => 'GBP']);
        DB::table('countries')->where('id', '80')->where('short_name', 'DE')->update(['currency_code' => 'EUR']);
    }
}
