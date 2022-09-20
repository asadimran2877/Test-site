<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NotificationSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\NotificationSetting::insert(
            [
                [
                    'notification_type_id' => '7',
                    'recipient_type'       => 'email',
                    'recipient'            => NULL,
                    'status'               => 'No',
                ],
    
                [
                    'notification_type_id' => '7',
                    'recipient_type'       => 'sms',
                    'recipient'            => NULL,
                    'status'               => 'No',
                ]
            ]
        );
    }
}
