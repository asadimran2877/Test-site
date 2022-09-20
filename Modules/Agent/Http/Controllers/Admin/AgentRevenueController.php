<?php

namespace Modules\Agent\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Agent\Exports\AgentRevenuesExport;
use Modules\Agent\DataTables\AgentRevenuesDataTable;

class AgentRevenueController extends Controller
{
    protected $transaction;

    public function __construct()
    {
        $this->revenue = new Transaction();
    }

    public function revenueList(AgentRevenuesDataTable $dataTable)
    {
        $data['menu'] = 'agents_revenues';
        
        $revenueDatas = $this->revenue->where(function ($query) {
                $query->where('charge_percentage', '>', 0);
                $query->orWhere('charge_fixed', '!=', 0);
            })->where('status', 'Success')
            ->whereIn('transaction_type_id', [Deposit, Withdrawal]);

        $data['revenues_currency'] = $revenueDatas->groupBy('currency_id')->select('currency_id')->get();
        $data['revenues_type'] = $revenueDatas->groupBy('transaction_type_id')->select('transaction_type_id')->distinct()->get();

        $data['from'] = $from = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = $to = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['currency'] = $currency = isset(request()->currency) ? request()->currency : 'all';
        $data['type'] = $type = isset(request()->type) ? request()->type : 'all';
        $data['agent'] = $user = isset(request()->user_id) ? request()->user_id : null;

        if (!empty($user)) {
            $data['getName'] = $this->revenue->getTransactionsUsersAgentsName($user, null);
        }

        $getRevenuesListForCurrencyIfo = $this->revenue->getRevenuesList($from, $to, $currency, $type, $data['agent'])->orderBy('transactions.id', 'desc')->get();

        $array = $codes =[];

        if ($getRevenuesListForCurrencyIfo->count() > 0) {
            foreach ($getRevenuesListForCurrencyIfo as $value) {
                if (isset($value->currency->code)) {
                    if (!in_array($value->currency->code, $codes)) {
                        $array[$value->currency->code]['revenue'] = 0;
                        $array[$value->currency->code]['currency_id'] = $value->currency->id;
                        $codes[] = $value->currency->code;
                    }
                    $array[$value->currency->code]['revenue'] += ($value->agent_percentage);
                }
            }
            $data['currencyInfo'] = $array;
        } else {
            $data['currencyInfo'] = [];
        }
        return $dataTable->render('agent::admin.agent.revenues.list', $data);
    }

    public function revenuesUserSearch(Request $request)
    {
        $search = $request->search;
        $user = $this->revenue->getTransactionsAgentResponse($search, null);
        $res = [
            'status' => 'fail',
        ];
        if (count($user) > 0) {
            $res = [
                'status' => 'success',
                'data' => $user,
            ];
        }
        return json_encode($res);
    }

    public function revenueCsv()
    {
        return Excel::download(new AgentRevenuesExport(), 'revenues_list_' . time() . '.xlsx');
    }

    public function revenuePdf()
    {
        $from = isset(request()->startfrom) && !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to = isset(request()->endto) && !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $type = isset(request()->type) ? request()->type : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $agent = isset(request()->user_id) ? request()->user_id : null;

        $data['revenues'] = (new Transaction())->getRevenuesList($from, $to, $currency, $type, $agent)->orderBy('transactions.id', 'desc')->get();
        if (isset($from) && isset($to)) {
            $data['date_range'] = $from. ' To ' . $to;
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
        $mpdf->WriteHTML(view('agent::admin.agent.revenues.revenues_report_pdf', $data));
        $mpdf->Output('revenues_report_' . time() . '.pdf', 'D');
    }
}
