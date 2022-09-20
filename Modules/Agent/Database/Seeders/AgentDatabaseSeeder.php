<?php

namespace Modules\Agent\Database\Seeders;

use DB;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Agent\Database\Seeders\{MetasTableSeeder, 
    PermissionsTableSeeder, 
    EmailTemplatesTableSeeder,
    PaymentMethodsTableSeeder
};

class AgentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(MetasTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);
    }
}
