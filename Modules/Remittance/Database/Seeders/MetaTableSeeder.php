<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\Meta::insert(
            [
                ['url' => 'remittance/index', 'title' => 'Remittance', 'description' => 'Remittance', 'keywords' => ''],

                ['url' => 'remittance/recepient-details', 'title' => 'Recepient Details', 'description' => 'Recepient Details', 'keywords' => ''],
    
                ['url' => 'remittance/delivered/details', 'title' => 'Transfer Details', 'description' => 'Transfer Details', 'keywords' => ''],
    
                ['url' => 'remittance/stripe_payment', 'title' => 'Remittance Payment', 'description' => 'Remittance Payment', 'keywords' => ''],
    
                ['url' => 'remittance/stripe-payment/success', 'title' => 'Remittance Successful', 'description' => 'Remittance Successful', 'keywords' => ''],
    
                ['url' => 'remittance/transfer-summery', 'title' => 'Remittance Payment', 'description' => 'Remittance Payment', 'keywords' => ''],
    
                ['url' => 'remittance/paypal-payment/success/{amount}', 'title' => 'Remittance Successful', 'description' => 'Remittance Successful', 'keywords' => '']
            ]
        );
    }
}
