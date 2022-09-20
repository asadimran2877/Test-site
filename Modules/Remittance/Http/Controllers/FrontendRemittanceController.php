<?php

namespace Modules\Remittance\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Contracts\Support\Renderable;

class FrontendRemittanceController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper  = new Common();
    }
    public function homePageRemittance(Request $request)
    {
        $remittance = json_encode($request->all());
        if (isset($request->send_currency) && ($request->send_amount != null)) {
            Cookie::queue('remittance', $remittance, 10);
            if (Auth::check()) {
                return redirect('remittance/remittanceRedirectTo');
            } else {
                $this->helper->one_time_message('error', __('Please login first !'));
                return redirect('login');
            }
        } else {
            return redirect()->back();
        }
    }
   
}
