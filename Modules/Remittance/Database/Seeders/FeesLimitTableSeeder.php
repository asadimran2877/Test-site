<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class FeesLimitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\FeesLimit::insert(
            [
                [
                    'currency_id'         => 1,
                    'transaction_type_id' => 11,
                    'payment_method_id'   => 2,
                    'charge_percentage'   => 0.00000000,
                    'charge_fixed'        => 0.00000000,
                    'min_limit'           => 1.00000000,
                    'max_limit'           => null,
                    'has_transaction'     => 'Yes',
                ],
                [
                    'currency_id'         => 1,
                    'transaction_type_id' => 11,
                    'payment_method_id'   => 3,
                    'charge_percentage'   => 0.00000000,
                    'charge_fixed'        => 0.00000000,
                    'min_limit'           => 1.00000000,
                    'max_limit'           => null,
                    'has_transaction'     => 'Yes',
                ],
            ]
        );
    }
}
