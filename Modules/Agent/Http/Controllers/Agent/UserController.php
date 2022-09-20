<?php

namespace Modules\Agent\Http\Controllers\Agent;

use Modules\Agent\Http\Requests\{CreateUserRequest,
    UpdateUserRequest
};
use App\Http\Controllers\Users\EmailController;
use Illuminate\Routing\Controller;
use DB, Auth, Validator, Common;
use Illuminate\Http\Request;
use App\Models\{User,
    UserDetail,
    RoleUser,
    Country,
    Role
};

class UserController extends Controller
{
    protected $helper;
    protected $email;
    protected $user;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
        $this->user = new User();
    }

    public function list() {
        if (Auth::guard('agent')->user()->type == 'Agent') {
            $data['menu'] = 'user';
            $data['sub_menu'] = 'user';
            $data['list'] = User::where(['agent_id' => Auth::guard('agent')->user()->id])->paginate(10);
            return view('agent::agent.agent_dashboard.user.list', $data);
        }
    }

    public function add()
    {
        if (Auth::guard('agent')->user()->type == 'Agent') {
            $data['menu'] = 'user';
            $data['sub_menu'] = 'user';

            $data['roles'] = Role::select('id', 'display_name')->where('user_type', "User")->get();

            return view('agent::agent.agent_dashboard.user.add', $data);
        }
    }

    public function storeUser(CreateUserRequest $request)
    {
        if ($request->isMethod('post')) {
            try {
                DB::beginTransaction();
                // Create user
                $user = $this->user->createNewUser($request, 'agent');

                RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                $this->user->createUserDetail($user->id);

                // Create user's default wallet
                $this->user->createUserDefaultWallet($user->id, settings('default_currency'));

                // Create wallets that are allowed by admin
                if (settings('allowed_wallets') != 'none') {
                    $this->user->createUserAllowedWallets($user->id, settings('allowed_wallets'));
                }
                DB::commit();
                
                $this->userCreateByagentNotification($user);

                $this->helper->one_time_message('success', __('User Created Successfully'));
                return redirect('agent/user');

            } catch (\Exception $e) {
                DB::rollBack();
                $this->helper->one_time_message('error', __('User Created Failed'));
                return back();
            }
        }
    }

    public function edit($id)
    {
        if (Auth::guard('agent')->user()->type == 'Agent') {
            $data['menu'] = 'user';
            $data['sub_menu'] = 'user'; 
            $data['user'] = User::find($id);
            
            return view('agent::agent.agent_dashboard.user.edit', $data);
        }
    }

    public function updateUser(UpdateUserRequest $request)
    {
        if ($request->isMethod('post')) {
            try {
                DB::beginTransaction();
                $user = User::find($request->id);
                $user->first_name = $request->first_name;
                $user->last_name  = $request->last_name;
                $user->status     = $request->status;

                $formattedPhone = ltrim($request->phone, '0');
                if (!empty($request->phone)) {
                    $user->phone          = preg_replace("/[\s-]+/", "", $formattedPhone);
                    $user->defaultCountry = $request->defaultCountry;
                    $user->carrierCode    = $request->carrierCode;
                    $user->formattedPhone = $request->formattedPhone;
                } else {
                    $user->phone          = null;
                    $user->defaultCountry = null;
                    $user->carrierCode    = null;
                    $user->formattedPhone = null;
                }

                if (!is_null($request->password) && !is_null($request->password_confirmation)) {
                    $user->password = \Hash::make($request->password);
                }
                $user->save();

                DB::commit();

                $this->userUpdateByagentNotification($user);

                $this->helper->one_time_message('success', __('User updated successfully.'));
                return redirect('agent/user');
            } catch (Exception $e) {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('agent/user');
            }
        } 
    }

    public function show($id)
    {
        if (Auth::guard('agent')->user()->type == 'Agent') {
            $data['menu'] = 'user';
            $data['sub_menu'] = 'user';
            $data['user'] = User::find($id);
            return view('agent::agent.agent_dashboard.user.view', $data);
        }
    }

    public function destroyUser($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {
            if ($user) {
                //Soft Delete User
                $user->status = 'Inactive';
                $user->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return back();
        }

        DB::commit();
        $this->helper->one_time_message('success', __('User deleted successfully.'));
        return redirect('agent/user');
    }

    public function userCreateByagentNotification($user)
    {
        if (checkAppMailEnvironment())
        {            
            $englishSenderLanginfo = $this->helper->getEmailOrSmsTemplate(37, 'email');
            $sender_info = $this->helper->getEmailOrSmsTemplate(37, 'email', settings('default_language'));

            if (!empty($sender_info->subject) && !empty($sender_info->body)) {
                $sender_subject = $sender_info->subject;
                $sender_msg     = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $sender_info->body);
            } else {
                $sender_subject = $englishSenderLanginfo->subject;
                $sender_msg     = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishSenderLanginfo->body);
            }
            $sender_msg = str_replace('{email}', $user->email, $sender_msg);
            $sender_msg = str_replace('{agent}', Auth::guard('agent')->user()->first_name. '' .Auth::guard('agent')->user()->last_name, $sender_msg);
            $sender_msg = str_replace('{status}', $user->status, $sender_msg);
            $sender_msg = str_replace('{login_url}', url('/login'), $sender_msg);
            $sender_msg = str_replace('{soft_name}', settings('name'), $sender_msg);
            try {
                $this->email->sendEmail($user->email, $sender_subject, $sender_msg);
            } catch (Exception $e) {
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('agent/user');
            }
        }
    }

    public function userUpdateByagentNotification($user)
    {
        if (checkAppMailEnvironment())
        {            
            $englishSenderLanginfo = $this->helper->getEmailOrSmsTemplate(38, 'email');
            $sender_info = $this->helper->getEmailOrSmsTemplate(38, 'email', settings('default_language'));

            if (!empty($sender_info->subject) && !empty($sender_info->body)) {
                $sender_subject = $sender_info->subject;
                $sender_msg     = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $sender_info->body);
            } else {
                $sender_subject = $englishSenderLanginfo->subject;
                $sender_msg     = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishSenderLanginfo->body);
            }
            $sender_msg = str_replace('{agent}', Auth::guard('agent')->user()->first_name. '' .Auth::guard('agent')->user()->last_name, $sender_msg);
            $sender_msg = str_replace('{status}', $user->status, $sender_msg);
            $sender_msg = str_replace('{soft_name}', settings('name'), $sender_msg);
            try {
                $this->email->sendEmail($user->email, $sender_subject, $sender_msg);
            } catch (Exception $e) {
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('agent/user');
            }
        }
    }
}
