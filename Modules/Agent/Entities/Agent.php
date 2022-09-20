<?php

namespace Modules\Agent\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Agent\Entities\AgentWallet;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Http\Helpers\Common;

class Agent extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $table = 'agents';

    protected $fillable = [
        'first_name', 'last_name', 'picture', 'phone', 'defaultCountry', 'carrierCode', 'email', 'password', 'status',
    ];

    protected static function newFactory()
    {
        return \Modules\Agent\Database\factories\AgentFactory::new ();
    }

    public function activityLog()
    {
        return $this->hasOne(\App\Models\ActivityLog::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function deposit()
    {
        return $this->hasOne(\App\Models\Deposit::class);
    }

    public function agentWallets()
    {
        return $this->hasMany(AgentWallet::class);
    }

    public function createOrUpdateAgent($request, $method)
    {
        $agent = ($method == 'Create') ? new Agent() : Agent::find($request->id);
        $agent->first_name = $request->first_name;
        $agent->last_name = $request->last_name;
        $agent->type = 'Agent';

        if (!empty($request->phone)) {
            $agent->phone = $request->phone;
            $agent->defaultCountry = $request->defaultCountry;
            $agent->carrierCode = $request->carrierCode;
            $agent->formattedPhone = $request->formattedPhone;
        } else {
            $agent->phone = null;
            $agent->defaultCountry = null;
            $agent->carrierCode = null;
            $agent->formattedPhone = null;
        }

        $agent->email = $request->email;
        if (!is_null($request->password) && !is_null($request->confirm_password)) {
            $agent->password = \Hash::make($request->password);
        }
        $agent->status = isset($request->status) && !empty($request->status) ? $request->status : 'Active';
        $agent->save();

        return $agent;
    }

    public function createAgentDefaultWallet($agentId, $defaultCurrency)
    {
        $agentWallet              = new AgentWallet();
        $agentWallet->agent_id    = $agentId;
        $agentWallet->currency_id = $defaultCurrency;
        $agentWallet->is_default  = 'Yes';
        $agentWallet->save();
    }

    public function createAgentAllowedWallets($agentId, $allowedWalletCurrencies)
    {
        $currencies = explode(',', $allowedWalletCurrencies);

        foreach($currencies as $currencyId) 
        {
            $agentWallet = new AgentWallet();
            $agentWallet->agent_id    = $agentId;
            $agentWallet->currency_id = $currencyId;
            $agentWallet->is_default  = 'No';
            $agentWallet->save();
        }
    }

    public function agentRegistrantionNotification($agent, $pass)
    {
        $common                                = new Common();
        $englishAgentNotificationEmailTempInfo = $common->getEmailOrSmsTemplate(39, 'email');
        $AgentNotificationEmailTempInfo        = $common->getEmailOrSmsTemplate(39, 'email', settings('default_language'));
        if (!empty($AgentNotificationEmailTempInfo->subject) && !empty($AgentNotificationEmailTempInfo->body)) {
            $AgentNotificationEmailTempInfo_sub = $AgentNotificationEmailTempInfo->subject;
            $AgentNotificationEmailTempInfo_msg = str_replace('{agent}', $agent->first_name . ' ' . $agent->last_name, $AgentNotificationEmailTempInfo->body);
        } else {
            $AgentNotificationEmailTempInfo_sub = $englishAgentNotificationEmailTempInfo->subject;
            $AgentNotificationEmailTempInfo_msg = str_replace('{agent}', $agent->first_name . ' ' . $agent->last_name, $englishAgentNotificationEmailTempInfo->body);
        }
        $AgentNotificationEmailTempInfo_msg = str_replace('{email}', $agent->email, $AgentNotificationEmailTempInfo_msg);
        $AgentNotificationEmailTempInfo_msg = str_replace('{pass}', $pass, $AgentNotificationEmailTempInfo_msg);
        $AgentNotificationEmailTempInfo_msg = str_replace('{reset_pass_url}', url('agent/forget-password'), $AgentNotificationEmailTempInfo_msg);
        $AgentNotificationEmailTempInfo_msg = str_replace('{soft_name}', settings('name'), $AgentNotificationEmailTempInfo_msg);

        return [
            'email'   => $agent->email,
            'subject' => $AgentNotificationEmailTempInfo_sub,
            'message' => $AgentNotificationEmailTempInfo_msg,
        ];
    }

    public function agentUpdateNotification($agent)
    {
        $common                                = new Common();
        $englishAgentNotificationEmailTempInfo = $common->getEmailOrSmsTemplate(40, 'email');
        $AgentNotificationEmailTempInfo        = $common->getEmailOrSmsTemplate(40, 'email', settings('default_language'));
        if (!empty($AgentNotificationEmailTempInfo->subject) && !empty($AgentNotificationEmailTempInfo->body)) {
            $AgentNotificationEmailTempInfo_sub = $AgentNotificationEmailTempInfo->subject;
            $AgentNotificationEmailTempInfo_msg = str_replace('{agent}', $agent->first_name . ' ' . $agent->last_name, $AgentNotificationEmailTempInfo->body);
        } else {
            $AgentNotificationEmailTempInfo_sub = $englishAgentNotificationEmailTempInfo->subject;
            $AgentNotificationEmailTempInfo_msg = str_replace('{agent}', $agent->first_name . ' ' . $agent->last_name, $englishAgentNotificationEmailTempInfo->body);
        }
        $AgentNotificationEmailTempInfo_msg = str_replace('{email}', $agent->email, $AgentNotificationEmailTempInfo_msg);
        $AgentNotificationEmailTempInfo_msg = str_replace('{status}', $agent->status, $AgentNotificationEmailTempInfo_msg);
        $AgentNotificationEmailTempInfo_msg = str_replace('{soft_name}', settings('name'), $AgentNotificationEmailTempInfo_msg);

        return [
            'email'   => $agent->email,
            'subject' => $AgentNotificationEmailTempInfo_sub,
            'message' => $AgentNotificationEmailTempInfo_msg,
        ];
    }

    public function userDepositByAgentNotification($deposit)
    {
        $common = new Common();
        if (checkAppMailEnvironment()) {
            $english_deposit_email_temp = $common->getEmailOrSmsTemplate(41, 'email');
            $deposit_email_temp         = $common->getEmailOrSmsTemplate(41, 'email', settings('default_language'));
            
            if (!empty($english_deposit_email_temp->subject) && !empty($english_deposit_email_temp->body)) {
                $d_success_sub = str_replace('{uuid}', $deposit->uuid, $english_deposit_email_temp->subject);
                $d_success_msg = str_replace('{agent}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $english_deposit_email_temp->body);
            } else {
                $d_success_sub = str_replace('{uuid}', $deposit->uuid, $deposit_email_temp->subject);
                $d_success_msg = str_replace('{agent}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $deposit_email_temp->body);
            }
            
            $d_success_msg = str_replace('{amount}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)), $d_success_msg);
            $d_success_msg = str_replace('{created_at}', dateFormat($deposit->created_at, $deposit->user_id), $d_success_msg);
            $d_success_msg = str_replace('{uuid}', $deposit->uuid, $d_success_msg);
            $d_success_msg = str_replace('{code}', $deposit->currency->code, $d_success_msg);
            $d_success_msg = str_replace('{amount}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)), $d_success_msg);
            $d_success_msg = str_replace('{fee}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->charge_fixed + $deposit->charge_percentage)), $d_success_msg);
            $d_success_msg = str_replace('{soft_name}', settings('name'), $d_success_msg);
            

            return [
                'email'   => $deposit->user->email,
                'subject' => $d_success_sub,
                'message' => $d_success_msg,
            ];
        }
    }

    public function userDepositByAgentSmsNotification($deposit)
    {
        $common = new Common();
        if (checkAppSmsEnvironment()) {
            if (!empty($deposit->user->carrierCode) && !empty($deposit->user->phone)) {

                $enAgentDepositSmsTempInfo = $common->getEmailOrSmsTemplate(23, 'sms');
                $agentDepositSmsTempInfo = $common->getEmailOrSmsTemplate(23, 'sms', settings('default_language'));

                if (!empty($agentDepositSmsTempInfo->subject) && !empty($agentDepositSmsTempInfo->body)) {
                    $agentDepositSmsTempInfo_sub = $agentDepositSmsTempInfo->subject;
                    $agentDepositSmsTempInfo_msg = str_replace('{agent}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $agentDepositSmsTempInfo->body);
                } else {
                    $agentDepositSmsTempInfo_sub = $enAgentDepositSmsTempInfo->subject;
                    $agentDepositSmsTempInfo_msg = str_replace('{agent}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $enAgentDepositSmsTempInfo->body);
                }
                $agentDepositSmsTempInfo_msg = str_replace('{uuid}', $deposit->uuid, $agentDepositSmsTempInfo_msg);
                $agentDepositSmsTempInfo_msg = str_replace('{amount}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)), $agentDepositSmsTempInfo_msg);
                $agentDepositSmsTempInfo_msg = str_replace('{created_at}', dateFormat($deposit->created_at, $deposit->user_id), $agentDepositSmsTempInfo_msg);
                $agentDepositSmsTempInfo_msg = str_replace('{code}', $deposit->currency->code, $agentDepositSmsTempInfo_msg);
                $agentDepositSmsTempInfo_msg = str_replace('{fee}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->charge_fixed + $deposit->charge_percentage)), $agentDepositSmsTempInfo_msg);
                $agentDepositSmsTempInfo_msg = str_replace('{soft_name}', settings('name'), $agentDepositSmsTempInfo_msg);
                
                if (!empty($deposit->user->formattedPhone)) {
                    sendSMS($deposit->user->carrierCode . $deposit->user->phone, $agentDepositSmsTempInfo_msg);
                }
            }
        }
    }

    public function withdrawNotificationsend($withdrawal)
    {
        $common = new Common();
        if (checkAppMailEnvironment()) {
            $english_withdrawal_email_temp = $common->getEmailOrSmsTemplate(42, 'email');
            $withdrawal_email_temp = $common->getEmailOrSmsTemplate(42, 'email', settings('default_language'));

            if (!empty($english_withdrawal_email_temp->subject) && !empty($english_withdrawal_email_temp->body)) {
                $w_success_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                $w_success_msg = str_replace('{agent}', $withdrawal->agent->first_name . ' ' . $withdrawal->agent->last_name, $english_withdrawal_email_temp->body);
            } else {
                $w_success_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                $w_success_msg = str_replace('{agent}', $withdrawal->agent->first_name . ' ' . $withdrawal->agent->last_name, $withdrawal_email_temp->body);
            }

            $w_success_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount)), $w_success_msg);
            $w_success_msg = str_replace('{created_at}', dateFormat($withdrawal->created_at, $withdrawal->user_id), $w_success_msg);
            $w_success_msg = str_replace('{uuid}', $withdrawal->uuid, $w_success_msg);
            $w_success_msg = str_replace('{code}', $withdrawal->currency->code, $w_success_msg);
            $w_success_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount)), $w_success_msg);
            $w_success_msg = str_replace('{fee}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->charge_fixed + $withdrawal->charge_percentage)), $w_success_msg);
            $w_success_msg = str_replace('{soft_name}', settings('name'), $w_success_msg);
            
            return [
                'email'   => $withdrawal->user->email,
                'subject' => $w_success_sub,
                'message' => $w_success_msg,
            ];
        }
    }
    
    public function withdrawSmsNotificationsend($withdrawal)
    {
        $common = new Common();
        if (checkAppSmsEnvironment()) {
            if (!empty($withdrawal->user->carrierCode) && !empty($withdrawal->user->phone)) {
                $agentWithdrawalSmsTempInfo = $common->getEmailOrSmsTemplate(24, 'sms');
                $enAgentWithdrawalSmsTempInfo = $common->getEmailOrSmsTemplate(24, 'sms', settings('default_language'));

                if (!empty($agentWithdrawalSmsTempInfo->subject) && !empty($agentWithdrawalSmsTempInfo->body)) {
                    $agentWithdrawalSmsTempInfo_sub = $agentWithdrawalSmsTempInfo->subject;
                    $agentWithdrawalSmsTempInfo_msg = str_replace('{agent}', $agentFullName, $agentWithdrawalSmsTempInfo->body);
                } else {
                    $agentWithdrawalSmsTempInfo_sub = $enAgentWithdrawalSmsTempInfo->subject;
                    $agentWithdrawalSmsTempInfo_msg = str_replace('{agent}', $agentFullName, $enAgentWithdrawalSmsTempInfo->body);
                }
                $agentWithdrawalSmsTempInfo_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount)), $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{created_at}', dateFormat($withdrawal->created_at, $withdrawal->user_id), $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{uuid}', $withdrawal->uuid, $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{code}', $withdrawal->currency->code, $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{amount}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->amount)), $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{fee}', moneyFormat($withdrawal->currency->symbol, formatNumber($withdrawal->charge_fixed + $withdrawal->charge_percentage)), $agentWithdrawalSmsTempInfo_msg);
                $agentWithdrawalSmsTempInfo_msg = str_replace('{soft_name}', settings('name'), $agentWithdrawalSmsTempInfo_msg);
                
                if (!empty($withdrawal->user->formattedPhone)) {
                    sendSMS($withdrawal->user->carrierCode . $withdrawal->user->phone, $agentWithdrawalSmsTempInfo_msg);
                }
            }
        }
    }
}
