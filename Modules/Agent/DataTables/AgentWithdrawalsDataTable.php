<?php

namespace Modules\Agent\DataTables;

use App\Models\Withdrawal;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\{Auth, 
    Config
};
use Yajra\DataTables\Services\DataTable;

class AgentWithdrawalsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($withdrawal) {
                return dateFormat($withdrawal->created_at);
            })
            ->addColumn('user_id', function ($withdrawal) {
                $senderWithLink = '-';
                if (isset($withdrawal->user->first_name) && !empty($withdrawal->user->first_name)) {
                    $sender = $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name;
                    $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $withdrawal->user->id) . '">' . $sender . '</a>' : $sender;
                }
                return $senderWithLink;   
            })
            ->editColumn('amount', function ($withdrawal) {
                return formatNumber($withdrawal->amount, $withdrawal->currency_id);
            })
            ->addColumn('fees', function ($withdrawal) {
                return ($withdrawal->charge_percentage == 0) && ($withdrawal->charge_fixed == 0) ? '-' : formatNumber($withdrawal->charge_percentage + $withdrawal->charge_fixed, $withdrawal->currency_id);
            })
            ->addColumn('total', function ($withdrawal) {
                $total = $withdrawal->charge_percentage + $withdrawal->charge_fixed + $withdrawal->amount;
                return '<td><span class="text-'. (($total > 0) ? 'green">' : 'red">')  . formatNumber($total, $withdrawal->currency_id) . '</span></td>';
            })
            ->editColumn('currency_id', function ($withdrawal) {
                return isset($withdrawal->currency->code) ? $withdrawal->currency->code : '-';
            })
            ->editColumn('status', function ($withdrawal) {
                return getStatusLabel($withdrawal->status);
            })
            ->addColumn('action', function ($withdrawal) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_withdrawal')) ?
                '<a href="' . url(Config::get('adminPrefix') . '/withdrawals/edit/' . $withdrawal->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['user_id', 'total', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = Withdrawal::with(['user:id,first_name,last_name', 'currency:id,code,symbol'])->where(['withdrawals.agent_id' => $this->agent_id, 'payment_method_id' => Cash])->select('withdrawals.id', 'uuid', 'amount', 'charge_fixed', 'charge_percentage', 'withdrawals.status', 'user_id', 'currency_id', 'withdrawals.created_at');

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'withdrawals.id', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'withdrawals.created_at', 'title' => __('Date')])
            ->addColumn(['data' => 'uuid', 'name' => 'withdrawals.uuid', 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.last_name', 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.first_name', 'title' => __('User')])
            ->addColumn(['data' => 'amount', 'name' => 'withdrawals.amount', 'title' => __('Amount')])
            ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => __('Fees')])
            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total')])
            ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => __('Currency')])
            ->addColumn(['data' => 'status', 'name' => 'withdrawals.status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
