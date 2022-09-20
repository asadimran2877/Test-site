<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\EmailTemplate::insert(
            [
                // Remittance Notification email to Admin
                [
                    'temp_id'     => '33',
                    'subject'     => 'Notice of Remittance Notification!',
                    'body'        => 'Hi,                               
                                        <br><br>Amount {amount} was sent by {user}.                               <br><br><b><u><i>Here’s a brief overview of the Remittance:</i></u>
                                        </b>                               
                                        <br><br><b><u>Created at:</u></b> {created_at}                               
                                        <br><br><b><u>Transaction ID:</u></b> {uuid}
                                                                    
                                        <br><br><b><u>Amount:</u></b> {amount}
                                                                    
                                        <br><br><b><u>Fee:</u></b> {fee}
                                                                    
                                        <br><br>If you have any questions, please feel free to reply to this email.                               
                                        <br><br>Regards,
                                                                    
                                        <br><b>{soft_name}</b>',

                    'lang'        => 'en',
                    'type'        => 'email',
                    'language_id' => 1,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ar',
                    'type'        => 'email',
                    'language_id' => 2,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'fr',
                    'type'        => 'email',
                    'language_id' => 3,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'pt',
                    'type'        => 'email',
                    'language_id' => 4,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ru',
                    'type'        => 'email',
                    'language_id' => 5,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'es',
                    'type'        => 'email',
                    'language_id' => 6,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'tr',
                    'type'        => 'email',
                    'language_id' => 7,
                ],
                [
                    'temp_id'     => '33',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ch',
                    'type'        => 'email',
                    'language_id' => 8,
                ],

                // Remittance status update notification to user

                [
                    'temp_id'     => '35',
                    'subject'     => 'Status of Transaction #{uuid} has been updated!',
                    'body'        => 'Hi {sender_id},
                                        <br><br><b>
                                        Transaction of remittance #{uuid} has been updated to {status} by system administrator!</b>
                                                                                                    
                                        <br><br>If you have any questions, please feel free to reply to this email.                               
                                        <br><br>Regards,                               
                                        <br><b>{soft_name}</b>',
                    'lang'        => 'en',
                    'type'        => 'email',
                    'language_id' => 1,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ar',
                    'type'        => 'email',
                    'language_id' => 2,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'fr',
                    'type'        => 'email',
                    'language_id' => 3,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'pt',
                    'type'        => 'email',
                    'language_id' => 4,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ru',
                    'type'        => 'email',
                    'language_id' => 5,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'es',
                    'type'        => 'email',
                    'language_id' => 6,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'tr',
                    'type'        => 'email',
                    'language_id' => 7,
                ],

                [
                    'temp_id'     => '35',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ch',
                    'type'        => 'email',
                    'language_id' => 8,
                ],

                // Remittance notification to user

                [
                    'temp_id'     => '32',
                    'subject'     => 'Notice of Remittance!',
                    'body'        => 'Hi {sender_id},                               
                                        <br><br>You have sent amount {amount} to the system administrator.                               
                                        <br><br><b><u><i>Here’s a brief overview of your remittance details:</i></u>
                                        </b>                               
                                        <br><br><b><u>Transaction ID:</u></b># {uuid} 
                                        <br><br><b><u>Created at:</u></b>{created_at}                               
                                        <br><br><b><u>Amount:</u></b> {amount}
                                                                    
                                        <br><br><b><u>Fee:</u></b> {fee}                               <br><br><b><u>Payment Method:</u></b> {pm}                               
                                        <br><br><b><u>Recipient Email:</u></b> {rc_email}                               
                                        <br><br><b><u>Recipient received amount:</u></b> {rc_amount}                               
                                        <br><br><b><u>Status:</u></b> {status}
                                                                    
                                        <br><br>If you have any questions, please feel free to reply to this email.                               
                                        <br><br>Regards,                               
                                        <br><b>{soft_name}</b>',
                    'lang'        => 'en',
                    'type'        => 'email',
                    'language_id' => 1,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ar',
                    'type'        => 'email',
                    'language_id' => 2,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'fr',
                    'type'        => 'email',
                    'language_id' => 3,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'pt',
                    'type'        => 'email',
                    'language_id' => 4,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ru',
                    'type'        => 'email',
                    'language_id' => 5,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'es',
                    'type'        => 'email',
                    'language_id' => 6,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'tr',
                    'type'        => 'email',
                    'language_id' => 7,
                ],

                [
                    'temp_id'     => '32',
                    'subject'     => '',
                    'body'        => '',
                    'lang'        => 'ch',
                    'type'        => 'email',
                    'language_id' => 8,
                ],

            ]
        );
    }
}
