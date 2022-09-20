<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RemittancePayoutMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \Modules\Remittance\Entities\RemittancePayoutMethod::insert(
            [
                [
                    'id' => 1, 
                    'payout_type' => 'Bank'
                ],
                [
                    'id' => 2, 
                    'name' => 'MPesa'

                ],

            ]
        );
    }
}
