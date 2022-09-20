<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RemittanceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(TransactionTypesTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(EmailTemplateTableSeeder::class);
        $this->call(FeesLimitTableSeeder::class);
        $this->call(MetaTableSeeder::class);
        $this->call(NotificationTypesTableSeeder::class);
        $this->call(NotificationSettingsTableSeeder::class);
        $this->call(RemittancePayoutMethodsTableSeeder::class);

        
    }
}
