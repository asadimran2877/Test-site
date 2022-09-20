<?php



namespace App\Http\Controllers;



use Illuminate\Support\Facades\{App,

    Session

};

use Illuminate\Http\Request;

use App\Http\Helpers\Common;

use App\Models\{Language, Currency};



class HomeController extends Controller

{

    protected $helper;



    public function __construct()

    {

        $this->helper = new Common();

    }



    public function index()

    {

        $data         = [];

        $data['menu'] = 'home';

        if (config('remittance.is_active')) {
            $data['sendMoneyCurrencyList'] = Currency::with('country')
                ->whereHas('currency_payment_method', function ($cpm) {
                    $cpm->where('activated_for', 'like', "%deposit%")->where(function ($m) {
                        $m->where(['method_id' => 2])->orWhere(['method_id' => 3]);
                    });
                })->whereHas('fees_limit', function ($query) {
                    $query->where(['transaction_type_id' => Remittance, 'has_transaction' => 'Yes']);
                })
                ->where(['status' => 'Active', 'type' => 'fiat'])
                ->where('remittance_type', 'like', "%send%")
                ->get(['id', 'code'])->shuffle();

            $data['receivedMoneyCurrencyList'] = Currency::with('country')
                ->where(['status' => 'Active', 'type' => 'fiat'])
                ->where('remittance_type', 'like', "%receive%")
                ->whereNotNULL('remittance_payout_method_id')
                ->get(['id', 'code'])
                ->shuffle();
            $data['preference'] = preference('decimal_format_amount');
        }
        
        return view('frontend.home.index', $data);

    }



    public function setLocalization(Request $request)

    {

        $langShotCode = Language::where('status', 'active')->pluck('short_name')->toArray();



        if (!in_array($request->lang, $langShotCode))

        {

            return 0;

        }

        if (!$request->ajax())

        {

            return 0;

        }



        if ($request->lang)

        {

            App::setLocale($request->lang);

            Session::put('dflt_lang', $request->lang);

            return 1;

        }

        else

        {

            return 0;

        }

    }

}

