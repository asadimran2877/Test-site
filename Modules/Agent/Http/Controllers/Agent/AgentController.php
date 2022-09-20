<?php

namespace Modules\Agent\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{User,
    Wallet,
    Country,
    Preference,
    ActivityLog,
    EmailTemplate,
    PaymentMethod
};
use Illuminate\Support\Facades\{Hash,
    DB,
    Auth,
    Session,
    Artisan,
    Validator
};
use Modules\Agent\Entities\{Agent,
    AgentWallet
};
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Users\EmailController;

class AgentController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
    }
    public function login()
    {
        return redirect()->route('agent');
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $agent = Agent::where('email', $request['email'])->first(['id', 'email', 'password', 'status']);
        
        if (empty($agent)) {
            $this->helper->one_time_message('danger', __('The email not registered.'));
            return redirect()->route('agent');
        }

        if ($agent->status == 'Inactive') {
            $this->helper->one_time_message('danger', __('You are Blocked.'));
            return redirect()->route('agent');
        }

        if (Auth::guard('agent')->attempt(['email' => trim($request['email']), 'password' => trim($request['password'])])) {
            $preferences = Preference::get();
            
            if (!empty($preferences)) {
                foreach ($preferences as $pref) {
                    $pref_arr[$pref->field] = $pref->value;
                }
            }

            if (!empty($preferences)) {
                Session::put($pref_arr);
            }

            //default_timezone
            Session::put('dflt_timezone_agent', session('dflt_timezone'));


            // default_currency
            if (!empty(settings('default_currency'))) {
                Session::put('default_currency', settings('default_currency'));
            }

            // default_language
            if (!empty(settings('default_language'))) {
                Session::put('default_language', settings('default_language'));
            }

            // company_name
            if (!empty(settings('name'))) {
                Session::put('name', settings('name'));
            }

            // company_logo
            if (!empty(settings('logo'))) {
                Session::put('company_logo', settings('logo'));
            }

            $log = [];
            $log['user_id'] = Auth::guard('agent')->check() ? Auth::guard('agent')->user()->id : null;
            $log['type'] = 'Agent';
            $log['ip_address'] = $request->ip();
            $log['browser_agent'] = $request->header('user-agent');
            $log['created_at'] = DB::raw('CURRENT_TIMESTAMP');

            ActivityLog::create($log);

            return redirect()->route('agent.dashboard');
        } else {
            $this->helper->one_time_message('danger', __('Please Check Your Email/Password'));
            return redirect()->route('agent');
        }
    }

    public function agentWallet()
    {
        $data['menu'] = 'agentWallet';

        $id = Auth::guard('agent')->user()->id;
        $data['wallets'] = AgentWallet::with('currency')->where(['agent_id' => $id])->orderBy('id', 'desc')->get();
        $data['agents'] = Agent::findOrFail($id, ['id', 'first_name', 'last_name']);

        return view('agent::agent.agent_dashboard.agents.agentwallet', $data);
    }

    public function logout()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Auth::guard('agent')->logout();
        return redirect()->route('agent');
    }

    public function forgetPassword(Request $request)
    {
        $methodName = $request->getMethod();
        if ($methodName == "GET") {
            return view('agent::agent.auth.forgetPassword');
        } else {
            $email = $request->email;
            $agent = Agent::where('email', $email)->first();

            if (!$agent) {
                $this->helper->one_time_message('error', __('Email Address does not match.'));
                return back();
            }
            $data['email'] = $request->email;
            $data['token'] = $token = base64_encode(encryptIt(rand(1000000, 9999999) . '_' . $request->email));
            $data['created_at'] = date('Y-m-d H:i:s');

            DB::table('password_resets')->insert($data);

            $agentFullName = $agent->first_name . ' ' . $agent->last_name;
            $this->sendPasswordResetEmail($request->email, $token, $agentFullName);

            $this->helper->one_time_message('success', __('Password reset link has been sent to this :x email address.', ['x' => $email]));
            return back();
        }
    }

    public function sendPasswordResetEmail($toEmail, $token, $agentFullName)
    {
        $common                       = new Common();
        $engUserPasswordResetTempInfo = $common->getEmailOrSmsTemplate(18, 'email');
        $userPasswordResetTempInfo    = $common->getEmailOrSmsTemplate(18, 'email', settings('default_language'));

        if (!empty($userPasswordResetTempInfo->subject) && !empty($userPasswordResetTempInfo->body)) {
            $userPasswordResetTempInfo_sub = $userPasswordResetTempInfo->subject;
            $userPasswordResetTempInfo_msg = str_replace('{user}', $agentFullName, $userPasswordResetTempInfo->body);
        } else {
            $userPasswordResetTempInfo_sub = $engUserPasswordResetTempInfo->subject;
            $userPasswordResetTempInfo_msg = str_replace('{user}', $agentFullName, $engUserPasswordResetTempInfo->body);
        }
        $userPasswordResetTempInfo_msg = str_replace('{email}', $toEmail, $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{password_reset_url}', url('agent/password/resets', $token), $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{soft_name}', settings('name'), $userPasswordResetTempInfo_msg);

        if (checkAppMailEnvironment()) {
            $this->email->sendEmail($toEmail, $userPasswordResetTempInfo_sub, $userPasswordResetTempInfo_msg);
        }
        //Mail for Password Reset - end
    }

    public function verifyToken($token)
    {
        if (!$token) {
            $this->helper->one_time_message('error', __('Token not found.'));
            return back();
        }
        $reset = DB::table('password_resets')->where('token', $token)->first();
        if ($reset) {
            $data['token'] = $token;
            return view('agent::agent.auth.passwordForm', $data);
        } else {
            $this->helper->one_time_message('error', __('Token session has been destroyed. Please try to reset again.'));
            return back();
        }
    }

    public function confirmNewPassword(Request $request)
    {
        $token = $request->token;
        $password = $request->new_password;
        $confirm = DB::table('password_resets')->where('token', $token)->first(['email']);

        $agent = Agent::where('email', $confirm->email)->first();
        $agent->password = Hash::make($password);
        $agent->save();

        DB::table('password_resets')->where('token', $token)->delete();

        $this->helper->one_time_message('success', __('Password changed successfully.'));
        return redirect()->to('/agent');
    }

    public function profile()
    {
        $data['menu'] = 'profile';
        $data['sub_menu'] = 'profile';
        $data['agent'] = Agent::find(Auth::guard('agent')->user()->id);
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        $data['timezones'] = phpDefaultTimeZones();

        return view('agent::agent.agent_dashboard.agents.profile', $data);
    }

    public function updateProfilePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
        ]);

        $agent = Agent::where(['id' => Auth::guard('agent')->user()->id])->first();

        if (Hash::check($request->old_password, $agent->password)) {
            $agent->password = Hash::make($request->password);
            $agent->save();

            $this->helper->one_time_message('success', __('Password Updated Successfully'));
            return redirect('agent/profile');
        } else {
            $this->helper->one_time_message('error', __('Your Old Password is Wrong'));
            return redirect('agent/profile');
        }
    }

    public function updateProfileInfo(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = array(
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
            );

            $fieldNames = array(
                'first_name' => __('First Name'),
                'last_name' => __('Last Name'),
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                $agent = Agent::findOrFail(Auth::guard('agent')->user()->id);
                $agent->first_name = $request->first_name;
                $agent->last_name = $request->last_name;
                $agent->save();
            }
        }
        $this->helper->one_time_message('success', __('Profile Updated Successfully'));
        return redirect('agent/profile');
    }

    public function profileImage(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect('agent/profile');
        } else {
            $validator = Validator::make($request->all(),
            ['file' => 'mimes:jpeg,jpg,png,gif,svg|max:5120',],
            [
                'file.mimes' => __('File must an image'),
                'file.max' => __('Image size is too large')
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $fileName = '';
            $agent = Agent::find(Auth::guard('agent')->user()->id);
            $oldPicture = $agent->picture ?? null;
            $picture = $request->file;

            if (isset($picture)) {
                $response = uploadImage($picture, public_path('/images/agents/profile/'),'100*100', $oldPicture, '70*70');
                if ($response['status'] === true) {
                    $fileName = $response['file_name'];
                } else {
                    DB::rollBack();
                    $this->helper->one_time_message('error', $response['message']);
                    return back()->withErrors($validator)->withInput();
                }
            }
            if ($fileName != null) {
                $agent->picture = $fileName;
            }
            $agent->save();
            return $fileName;
        }
    }

    public function postEmailCheckUserCreation(Request $request)
    {
        $isAvailable = false;
        $data = ['message' => __('This Email has already Used')];
        $user = User::where(['email' => $request->email])->first(['id', 'email']);

        if (empty($user)) {
            $isAvailable = true;
            $data['message'] = __('This Email is available');
        } else {
            if ($request->user_id && $user->id == $request->user_id) {
                $isAvailable = true;
                $data['message'] = __('This Email is available');
            }
        }

        $data['status'] = $isAvailable;
        return json_encode($data);
    }

    public function postPhoneCheckUserCreation(Request $request)
    {
        $isAvailable = false;
        $data = ['message' => __('This Number has already Used')];
        $user = User::where(['phone' => $request->phone, 'carrierCode' => $request->carrierCode])->first(['id', 'phone', 'carrierCode']);

        if (empty($user)) {
            $isAvailable = true;
            $data['message'] = __('This Number is available');
        } else {
            if ($request->user_id && $user->id == $request->user_id) {
                $isAvailable = true;
                $data['message'] = __('This Number is available');
            }
        }

        $data['status'] = $isAvailable;
        return json_encode($data);
    }

    public function searchUser(Request $request)
    {
        $str = $request->term;

        $relatedUser0 = User::where('users.first_name', 'LIKE', '%' . $str . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $str . '%')
            ->orWhere('users.phone', 'LIKE', '%' . $str . '%')
            ->orWhere('users.email', 'LIKE', '%' . $str . '%')
            ->select('users.id', 'users.email', 'users.agent_id', 'users.phone', 'users.carrierCode', 'users.first_name', 'users.last_name', 'users.status')
            ->get();
        $relatedUser = $relatedUser0->where('status', 'Active');

        $myArr = array();

        if (!empty($relatedUser)) {
            foreach ($relatedUser as $result) {
                $phone = !empty($result->phone) ? ' - '. $result->carrierCode.$result->phone : '';
                $myArr[] = array(
                  "id" => $result->id,
                  "text" => $result->first_name .' '. $result->last_name .' - '. $result->email . $phone
                );
            }
        }
        return $myArr;
    }

    public function getFeesLimit(Request $request)
    {
        $amount  = (double) $request->amount;
        $paymentMethod = PaymentMethod::where(['name' => 'Cash', 'status' => 'Active'])->first();
        $success['payment_method']  = $paymentMethod->id;
        $userId = $request->user_id;

        
        $feesDetails = $this->helper->getFeesLimitObject([], $request->transaction_type_id, $request->currency_id, $paymentMethod->id, null, ['min_limit', 'max_limit', 'charge_percentage', 'charge_fixed', 'agent_percentage']);
        if ($feesDetails != null) {
            if ($feesDetails->max_limit == null) {
                $success['status'] = 200;
                if (($amount < $feesDetails->min_limit)) {
                    $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit, $request->currency_id);
                    $success['status']  = '401';
                }
            } else {
                $success['status'] = 200;
                if (($amount < $feesDetails->min_limit) || ($amount > $feesDetails->max_limit)) {
                    $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit, $request->currency_id) . __(' and Maximum amount ') . formatNumber($feesDetails->max_limit, $request->currency_id);
                    $success['status']  = '401';
                }
            }
        } else {
            $success['status']  = '401';
            $success['message'] = __('Fees Limit or Payment Method settings is not active for Withdraw.');
        }
        $agentId = Auth::guard('agent')->user()->id;
        $agentWallet = AgentWallet::where(['currency_id' => $request->currency_id, 'agent_id' => $agentId])->first();
        
        //Code for Amount Limit ends here

        //Code for Fees Limit Starts here
        if (empty($feesDetails)) {
            $feesPercentage            = 0;
            $feesFixed                 = 0;
            $agentPercentage           = 0;
            $totalFess                 = $feesPercentage + $feesFixed + $agentPercentage;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['agentFee']       = $agentPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess, $request->currency_id);
            $success['amount']         = $amount;
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage;
            $success['fFees']          = $feesFixed;
            $success['pFeesHtml']      = formatNumber($feesPercentage, $request->currency_id);
            $success['fFeesHtml']      = formatNumber($feesFixed, $request->currency_id);
            $success['aFeesHtml']      = formatNumber($agentPercentage, $request->currency_id);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = 0;
            $success['agentbalance']   = 0;
        } else {
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $agentPercentage           = $amount * ($feesDetails->agent_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed  + $agentPercentage;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['agentFee']       = $agentPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess, $request->currency_id);
            $success['amount']         = $amount;
            $success['totalAmount']    = $totalAmount;
            $success['pFeesHtml']      = formatNumber($feesDetails->charge_percentage, $request->currency_id);
            $success['fFeesHtml']      = formatNumber($feesDetails->charge_fixed, $request->currency_id);
            $success['aFeesHtml']      = formatNumber($feesDetails->agent_percentage, $request->currency_id);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            if (isset($userId)) {
                $wallet                = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $userId])->first(['balance']);
                $success['balance']    = isset($wallet->balance) ? $wallet->balance : 0;
            }
            $success['agentbalance']   = $agentWallet->available_balance ? $agentWallet->available_balance : 0;
        }
        return response()->json(['success' => $success]);
    }
}
