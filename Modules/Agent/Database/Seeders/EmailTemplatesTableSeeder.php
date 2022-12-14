<?php

namespace Modules\Agent\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmailTemplate;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        EmailTemplate::insert([
            // Notice for User Registration! 
            [
                'temp_id'     => '37',
                'subject'     => 'Notice for User Registration!',
                'body'        => 'Hi <b>{user}</b>, <br><br> Your email id&nbsp;<b>{email}</b> is registered as a user by Agent <b>{agent}</b>. Please click on the below link to login into your account,<br><br> <b>{</b><span style=\"font-weight: bold;\">login_url</span><b>}</b><b><br></b><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b><b> </b>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '37',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice of user update via agent!
            [
                'temp_id'     => '38',
                'subject'     => 'Notice of user update by agent!',
                'body'        => '<div> </div>Hi <b>{user}</b>, <br><br>Your status has been changed to <b>{status}</b> by the Agent <b>{agent}</b>.<b></b><br><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b>.<b></b><div><b><br></b></div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '38',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice for Agent Registration!
            [
                'temp_id'     => '39',
                'subject'     => 'Notice for Agent Registration!',
                'body'        => 'Hi <b>{agent}</b>,<b><br></b><br> You have registered as an agent <b>{email}</b> by the system administrator<b></b>.<div><br></div><div>Your email id: <b>{email}</b> and Password : <b>{pass}</b>.</div><div><br></div><div><b></b>Please reset your password to secure your account. Please click on the below link and reset your password.<br><br> <div><div><b>{reset_pass_url}</b></div></div><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b><div><b><br></b></div> </div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '39',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice for Agent Update
            [
                'temp_id'     => '40',
                'subject'     => 'Notice for Agent Update',
                'body'        => 'Hi <b>{agent}</b>, <br><br>Your status has been changed to <b>{status}</b> by the System Administrator <br><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b>.<div><b></b><div><b><br></b></div> </div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '40',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice of Deposit to user by agent!
            [
                'temp_id'     => '41',
                'subject'     => 'Notice of Deposit to user by agent!',
                'body'        => 'Hi, <br><br>Amount <b>{amount}</b> was deposited by Agent <b>{agent}</b>. <br><br><b><u><i>Here???s a brief overview of the Deposit:</i></u></b> <br><br><b><u>Created at:</u></b> {created_at} <br><br><b><u>Transaction ID:</u></b> {uuid} <br><br><b><u>Currency:</u></b> {code} <br><br><b><u>Amount:</u></b> {amount} <br><br><b><u>Fee:</u></b> {fee} <br><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b><div></div><div><b></b><div><b><br></b></div> </div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '41',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice of Withdrawal from user by agent!
            [
                'temp_id'     => '42',
                'subject'     => 'Notice of Withdrawal from user by agent!',
                'body'        => 'Hi, <br><br>Amount <b>{amount}</b> was withdrawn by Agent <b>{agent}</b>. <br><br><b><u><i></i></u></b><b><u><i>Here???s a brief overview of the withdrawal:</i></u></b> <br><br><b><u>Created at:</u></b> {created_at} <br><br><b><u>Transaction ID:</u></b> {uuid} <br><br><b><u>Currency:</u></b> {code} <br><br><b><u>Amount:</u></b> {amount} <br><br><b><u>Fee:</u></b> {fee} <br><br>If you have any questions, please feel free to reply to this email. <br><br>Regards, <br><b>{soft_name}</b><div><b></b><div><b><br></b></div> </div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '42',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Notice for Withdraw verification!
            [
                'temp_id'     => '43',
                'subject'     => 'Notice for Withdraw verification!',
                'body'        => 'Hi <b>{user}</b>, <br><br> Your Withdraw Verification code is: <b>{code}</b> <br><br>Regards, <br><b>{soft_name}</b><div><b></b><div><b><br></b></div> </div>',
                'lang'        => 'en',
                'type'        => 'email',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'email',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'email',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'email',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'email',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'email',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'email',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '43',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'email',
                'language_id' => 8,
            ],

            // Deposit SMS Notification to User by agent!
            [
                'temp_id'     => '23',
                'subject'     => 'Deposit Notification to User by agent!',
                'body'        => 'Hi,<br><br>Amount <b>{amount}</b> was deposited by Agent&nbsp;<b>{agent}</b>.<br><br><b><u><i>Here???s a brief overview of the Deposit:</i></u></b><br><br><b><u>Created at:</u></b> {created_at}<br><br><b><u>Transaction ID:</u></b> {uuid}<br><br><b><u>Currency:</u></b> {code}<br><br><b><u>Amount:</u></b> {amount}<div><br><b><u>Fee:</u></b> {fee}<br><br>If you have any questions, please feel free to reply to this email.<br><br>Regards,<br><b>{soft_name}</b></div>',
                'lang'        => 'en',
                'type'        => 'sms',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'sms',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'sms',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'sms',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'sms',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'sms',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'sms',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '23',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'sms',
                'language_id' => 8,
            ],

            // Withdrawal Notification from User by Agent!
            [
                'temp_id'     => '24',
                'subject'     => 'Withdrawal Notification from User by Agent!',
                'body'        => 'Hi,<br><br>Amount <b>{amount}</b> was payout by <b>{agent}</b>.<br><br><b><u><i>Here???s a brief overview of the Payout:</i></u></b><br><br><b><u>Created at:</u></b> {created_at}<br><br><b><u>Transaction ID:</u></b> {uuid}<br><br><b><u>Currency:</u></b> {code}<br><br><b><u>Amount:</u></b> {amount}<br><br><b><u>Fee:</u></b> {fee}<br><br>If you have any questions, please feel free to reply to this email.<br><br>Regards,<br><b>{soft_name}</b>',
                'lang'        => 'en',
                'type'        => 'sms',
                'language_id' => 1,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ar',
                'type'        => 'sms',
                'language_id' => 2,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'fr',
                'type'        => 'sms',
                'language_id' => 3,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'pt',
                'type'        => 'sms',
                'language_id' => 4,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ru',
                'type'        => 'sms',
                'language_id' => 5,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'es',
                'type'        => 'sms',
                'language_id' => 6,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'tr',
                'type'        => 'sms',
                'language_id' => 7,
            ],
            [
                'temp_id'     => '24',
                'subject'     => '',
                'body'        => '',
                'lang'        => 'ch',
                'type'        => 'sms',
                'language_id' => 8,
            ],
        ]);
    }
}
