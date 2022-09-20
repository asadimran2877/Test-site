<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\TransactionType::insert(
            [
                [
                    'id' => 11, 
                    'name' => 'Remittance'
                ],

            ]
        );
       
    }
}
