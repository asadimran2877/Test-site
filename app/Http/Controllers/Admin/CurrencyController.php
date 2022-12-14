<?php



namespace App\Http\Controllers\Admin;



use App\DataTables\Admin\CurrenciesDataTable;

use DB, Config, Storage, Common, Exception;

use Intervention\Image\Facades\Image;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\{

    Transaction,

    Currency,

    User,

    Wallet

};



class CurrencyController extends Controller

{

    protected $helper;

    protected $currency;



    public function __construct()

    {

        $this->helper = new Common();

        $this->currency = new Currency();

    }



    public function index(CurrenciesDataTable $dataTable)

    {

        $data['menu'] = 'currency';

        return $dataTable->render('admin.currencies.view', $data);

    }



    public function add(Request $request)

    {

        $data['menu'] = 'currency';

        if (config('remittance.is_active')) {
            $data['remittancePayoutMethods'] = \Modules\Remittance\Entities\RemittancePayoutMethod::get(['id', 'payout_type']);
        }

        if ($request->isMethod('post')) {

            $this->validate($request, [

                'name' => 'required',

                'code' => 'required|unique:currencies,code',

                'symbol' => 'required',

                'type' => 'required',

                'rate' => $request->type == 'fiat' ? 'required|numeric|min:0.0001' : '',

                'logo' => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',

                'exchange_from' => $request->type == 'fiat' ? 'required' : '',

                'address' => $request->type == 'crypto' ? 'required' : '',

                'status' => 'required',

            ], [

                'rate.min' => 'Please enter values greater than 0.0001',

                'code.unique' => 'The currency already exists',

                'exchange_from.required' => 'Exchange from is required' 

            ]);



            $getFiatCurrency = $this->currency->getCurrency(['code' => $request->code], ['code']);



            if (!empty($getFiatCurrency) && $getFiatCurrency->code === $request->code) {

                $this->helper->one_time_message('error', 'The currency already exists.');

                return redirect(Config::get('adminPrefix').'/settings/currency');

            }



            try {

                

                DB::beginTransaction();



                $currency = new Currency();

                $currency->name = $request->name;

                $currency->code = $request->code;

                $currency->symbol = $request->symbol;

                $currency->type = $request->type == 'fiat' ? 'fiat' : 'crypto';

                $currency->rate = $request->type == 'fiat' ? $request->rate : 0;

                $currency->address = $request->type == 'crypto' ?  $request->address : '';

                $currency->exchange_from = $request->exchange_from;

                $currency->status = $request->status == 'Active' ? 'Active' : 'Inactive';

                $currency->default = '0';

                $filename = $this->processCurrencyLogo('add', $request, null);

                $currency->logo = $filename;

                if (config('remittance.is_active')) {
                    if (isset($request->remittance_type)) {
                        $remittanceType = implode(',', $request->remittance_type);
                        $currency->remittance_type = $remittanceType;
                    }

                    if (isset($request->remittance_payout_method)) {
                        $remittancePayoutMethodId = implode(',', $request->remittance_payout_method);
                        $currency->remittance_payout_method_id = $remittancePayoutMethodId;
                    }
                }

                $currency->save();



                DB::commit();

                $this->helper->one_time_message('success', 'Currency added successfully.');

                return redirect(Config::get('adminPrefix').'/settings/currency');



            } catch (Exception $e) {

                DB::rollBack();

                $this->helper->one_time_message('error', $e->getMessage());

                return redirect(Config::get('adminPrefix').'/settings/currency');

            }

        }



        return view('admin.currencies.add', $data);

    }



    public function update(Request $request, $id)

    {

        $data['menu'] = 'currency';

        $data['result'] = $result = Currency::find($id);

        if (config('remittance.is_active')) {
            $data['remittanceType'] = $data['result']->toArray();
            $data['remittancePayoutMethod'] = \Modules\Remittance\Entities\RemittancePayoutMethod::get(['id','payout_type']);
        }


        if ($request->isMethod('post')) {



            $this->validate($request, [

                'name' => 'required',

                'code' => 'required|unique:currencies,code,' . $id,

                'symbol' => 'required',

                'type' => 'required',

                'rate' => $request->type == 'fiat' ? 'required|numeric|min:0.0001' : '',

                'logo' => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',

                'exchange_from' => $request->type == 'fiat' ? 'required' : '',

                'address' => $request->type == 'crypto' ? 'required' : '',

                'status' => $request->default_currency == '0' ? 'required' : '',

            ], [

                'rate.min' => 'Please enter values greater than 0.0001',

                'code.unique' => 'The currency already exists',

                'exchange_from.required' => 'Exchange from is required' 

            ]);



            

            $getCurrency = Currency::where('id', '!=', $id)->where(['code' => $request->code])->first(['code']);

            if (!empty($getCurrency) && $getCurrency->code === $request->code) {

                $this->helper->one_time_message('error', 'The currency ' . $request->code . ' already exists!');

                return redirect(Config::get('adminPrefix').'/settings/currency');

            }

            try {

                DB::beginTransaction();



                $currency = Currency::find($id);

                $currency->name = $request->name;

                $currency->symbol = $request->symbol;

                $currency->code = $request->code;

                $currency->type = $request->type == 'crypto' ? 'crypto' : 'fiat';

                $currency->rate = $request->type == 'fiat' ? $request->rate : 0;

                $currency->exchange_from = $request->exchange_from;

                $currency->address = $request->type == 'crypto' ?  $request->address : '';

                

                if ($request->default_currency == 1) {

                    $currency->status  = 'Active';

                    $currency->default = 1;

                } else {

                    $currency->status  = $request->status == 'Active' ? 'Active' : 'Inactive';

                    $currency->default = 0;

                }



                if (isset($request->logo)) {

                    $filename = $this->processCurrencyLogo('edit', $request, $currentLogo = $currency->logo);

                    $currency->logo = $filename;

                }

                if ($request->type == 'fiat') {

                    if ($request->create_wallet == 'on') {

                        $this->createUsersWallet($currency->id);

                        $currency->allowed_wallet_creation = 'Yes';

                    } else {

                        $currency->allowed_wallet_creation = 'No';

                    }

                }

                if (config('remittance.is_active')) {
                    if (isset($request->remittance_type)) {
                        $remittanceType = implode(',', $request->remittance_type);
                        $currency->remittance_type = isset($remittanceType) && !empty($remittanceType) ?  $remittanceType : null;
                    }

                    if (isset($request->remittance_payout_method)) {
                        $remittancePayoutMethodId = implode(',', $request->remittance_payout_method);
                        $currency->remittance_payout_method_id = isset($remittancePayoutMethodId) && !empty($remittancePayoutMethodId) ?  $remittancePayoutMethodId : null;
                    }
                }

                $currency->save();

                DB::commit();



                $this->helper->one_time_message('success', 'Currency Updated Successfully');

                return redirect(Config::get('adminPrefix').'/settings/currency');

            }

            catch (Exception $e)

            {

                DB::rollBack();

                $this->helper->one_time_message('error', $e->getMessage());

                return redirect(Config::get('adminPrefix').'/settings/currency');

            }

        }



        $data['currency'] = Currency::find($id);

        if (!empty($data['currency'])) {

            return view('admin.currencies.edit', $data);

        }

        $this->helper->one_time_message('error', 'Currency not found.');

        return redirect(Config::get('adminPrefix').'/settings/currency');

    }



    protected function processCurrencyLogo($type, $request, $currentLogo)

    {

        if ($request->hasFile('logo')) {

            $logo = $request->file('logo');

            if (isset($logo)) {

                $filename  = time() . '.' . $logo->getClientOriginalExtension();

                $extension = strtolower($logo->getClientOriginalExtension());

                $location  = public_path('uploads/currency_logos/' . $filename);

                $oldFilelocation  = public_path('uploads/currency_logos/' . $currentLogo);

                if (isset($currentLogo) && file_exists($oldFilelocation)) {

                    unlink($oldFilelocation);

                }

                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {

                    Image::make($logo)->resize(64, 64)->save($location);



                    if ($type == 'edit') {

                        //Old file assigned to a variable

                        $oldfilename = $currentLogo;



                        //Delete old photo

                        Storage::delete($oldfilename);



                        return $filename;

                    } else {

                        return $filename;

                    }

                } else {

                    $this->helper->one_time_message('error', 'Invalid Image Format!');

                }

            }

        }

    }



    public function deleteCurrencyLogo(Request $request)

    {

        $logo = $request->logo;



        if (isset($logo)) {

            $currency = Currency::where(['id' => $request->currency_id, 'logo' => $request->logo])->first();

            if ($currency) {

                Currency::where(['id' => $request->currency_id, 'logo' => $request->logo])->update(['logo' => null]);

                if ($logo != null) {

                    $dir = public_path('uploads/currency_logos/' . $logo);

                    if (file_exists($dir)) {

                        unlink($dir);

                    }

                }

                $data['success'] = 1;

                $data['message'] = 'Logo has been successfully deleted!';

            } else {

                $data['success'] = 0;

                $data['message'] = "No Record Found!";

            }

        }

        echo json_encode($data);

        exit();

    }



    public function delete($id)

    {

        $currency = Currency::find($id);

        if (!empty($currency)) {

            $transaction = Transaction::where(['currency_id' => $currency->id])->first();



            if (isset($transaction)) {

                $this->helper->one_time_message('error', 'This currency cannot be deleted. Already transaction exist.');

            } elseif (isset($currency) && $currency->default == 1) {

                $this->helper->one_time_message('error', 'Default currency can not be deleted.');

            } else {

                if (isset($currency)) {

                    $location = public_path('uploads/currency_logos/' . $currency->logo);

                    if (isset($currency->logo) && file_exists($location)) {

                        unlink($location);

                    }

                    $currency->delete();

                }

                $this->helper->one_time_message('success', 'Currency deleted successfully.');

            }

            return redirect(Config::get('adminPrefix').'/settings/currency');

        }

        $this->helper->one_time_message('error', 'Currency not found.');

        return redirect(Config::get('adminPrefix').'/settings/currency');

    }



    protected function createUsersWallet($currencyId)

    {

        $users = User::with(['wallets' => function ($q) use ($currencyId)

        {

            $q->where(['currency_id' => $currencyId]);

        }])

        ->where(['status' => 'Active'])

        ->get(['id']);



        if (!empty($users)) {

            foreach ($users as $user)

            {

                $getWalletObject = $this->helper->getUserWallet([], ['user_id' => $user->id, 'currency_id' => $currencyId], ['id']);

                if (empty($getWalletObject) && count($user->wallets) == 0) {

                    $wallet              = new Wallet();

                    $wallet->user_id     = $user->id;

                    $wallet->currency_id = $currencyId;

                    $wallet->is_default  = 'No';

                    $wallet->save();

                }

            }

        }

    }

}





