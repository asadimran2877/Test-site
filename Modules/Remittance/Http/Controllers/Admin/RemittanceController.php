<?php

namespace Modules\Remittance\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Users\EmailController;
use Illuminate\Http\Request;
use Config;
use Modules\Remittance\Entities\Remittance;
use App\Models\Transaction;
use App\Http\Helpers\Common;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Remittance\Exports\RemittancesExport;
use Modules\Remittance\DataTables\RemittancesDataTable;

use Illuminate\Routing\Controller;

class RemittanceController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper     = new Common();
        $this->remittance = new Remittance();
        $this->email      = new EmailController();
    }
    public function index(RemittancesDataTable $dataTable)
    {
        $data['menu'] = 'remittances';

        $data['d_status']     = $this->remittance->select('status')->groupBy('status')->get();
        $data['d_currencies'] = $this->remittance->with('currency:id,code')->select('transferred_currency_id')->groupBy('transferred_currency_id')->get();
        $data['d_pm']         = $this->remittance->with('payment_method:id,name')->select('payment_method_id')->whereNotNull('payment_method_id')->groupBy('payment_method_id')->get();

        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        
        $data['pm']       = isset(request()->payment_methods) ? request()->payment_methods : 'all';
        $data['user']     = $user    = isset(request()->user_id) ? request()->user_id : null;
        $data['getName']  = $this->remittance->getRemittancesUsersName($user);

        return $dataTable->render('remittance::admin.remittances.list', $data);
    }
    

    public function edit($id)
    {
        $data['menu']    = 'remittances';
        $data['remittance'] = $remittance = Remittance::find($id);

        $data['transaction'] = Transaction::select('transaction_type_id', 'status', 'transaction_reference_id', 'percentage', 'charge_fixed')
            ->where(['transaction_reference_id' => $remittance->id, 'status' => $remittance->status, 'transaction_type_id' => Remittance])
            ->first();

        return view('remittance::admin.remittances.edit', $data);
    }

    public function update(Request $request)
    {
        //Remittance
        if ($request->transaction_type == 'Remittance') {
            if ($request->status == 'Pending') //requested status
            {
                if ($request->transaction_status == 'Pending') {
                    $this->helper->one_time_message('success', __('Remittance is already Pending!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Success') {
                    $remittances         = Remittance::with('sender', 'currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    $tt = Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);
                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }
                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Blocked') {
                    $remittances         = Remittance::with('sender', 'currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);
                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }
                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                }
            } elseif ($request->status == 'Success') {
                if ($request->transaction_status == 'Success') //current status
                {
                    $this->helper->one_time_message('success', __('Remittance is already Successfull!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Blocked') //current status
                {
                    $remittances         = Remittance::with('sender', 'currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }

                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Pending') {
                    $remittances         = Remittance::with('sender', 'currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);
                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }

                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                }
            } elseif ($request->status == 'Blocked') {
                if ($request->transaction_status == 'Blocked') //current status
                {
                    $this->helper->one_time_message('success', __('Remittance is already Blocked!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Pending') //current status
                {
                    $remittances         = Remittance::with('sender', 'currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);
                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }
                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                } elseif ($request->transaction_status == 'Success') //current status
                {
                    $remittances         = Remittance::with('sender','currency')->find($request->id);
                    $remittances->status = $request->status;
                    $remittances->save();

                    Transaction::where([
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    if (checkAppMailEnvironment()) {
                        $emailArr = $this->remittance->remittanceStatusChangeMailToUser($remittances);
                        $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                             
                    }

                    $this->helper->one_time_message('success', __('Remittance Updated Successfully!'));
                    return redirect(Config::get('adminPrefix') . '/remittances');
                }
            }
            
        }
    }

    public function remittancesUserSearch(Request $request)
    {
        $search = $request->search;
        $user   = $this->remittance->getRemittancesUsersResponse($search);

        $res = [
            'status' => 'fail',
        ];
        if (count($user) > 0) {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }

    public function remittanceCsv()
    {
        return Excel::download(new RemittancesExport(), 'remittance_list_' . time() . '.xlsx');
    }

    public function remittancePdf()
    {

        $from = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;

        $to = !empty(request()->endto) ? setDateForDb(request()->endto) : null;

        $status = isset(request()->status) ? request()->status : null;

        $pm = isset(request()->payment_methods) ? request()->payment_methods : null;

        $currency = isset(request()->currency) ? request()->currency : null;

        $user = isset(request()->user_id) ? request()->user_id : null;

        $data['remittances'] = $this->remittance->getRemittancesList($from, $to, $status, $currency, $pm, $user)->orderBy('id', 'desc')->get();

        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
            $data['date_range'] = 'N/A';
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);

        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);

        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;

        $mpdf->WriteHTML(view('remittance::admin.remittances.remittances_report_pdf', $data));

        $mpdf->Output('remittances_report_' . time() . '.pdf', 'D');
    }
}
