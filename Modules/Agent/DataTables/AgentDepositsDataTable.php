<?php

namespace Modules\Agent\DataTables;

use App\Models\Deposit;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\{Auth, 
    Config
};
use Yajra\DataTables\Services\DataTable;

class AgentDepositsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($deposit) {
                return dateFormat($deposit->created_at);
            })
            ->addColumn('user_id', function ($deposit) {
                if (isset($deposit->user->first_name) && !empty($deposit->user->first_name)) {
                    $sender = $deposit->user->first_name . ' ' . $deposit->user->last_name;
                    $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $deposit->user->id) . '">' . $sender . '</a>' : $sender;
                }
                return $senderWithLink ?? '-';
            })
            ->editColumn('amount', function ($deposit) {
                return formatNumber($deposit->amount, $deposit->currency_id);
            })
            ->addColumn('fees', function ($deposit) {
                return ($deposit->charge_percentage == 0) && ($deposit->charge_fixed == 0) ? '-' : formatNumber($deposit->charge_percentage + $deposit->charge_fixed, $deposit->currency_id);
            })
            ->addColumn('total', function ($deposit) {
                $total = $deposit->charge_percentage + $deposit->charge_fixed + $deposit->amount;
                return '<td><span class="text-'. (($total > 0) ? 'green">+' : 'red">')  . formatNumber($total, $deposit->currency_id) . '</span></td>';
            })
            ->editColumn('currency_id', function ($deposit) {
                return isset($deposit->currency->code) ? $deposit->currency->code : '-';
            })
            ->editColumn('status', function ($deposit) {
                return getStatusLabel($deposit->status);
            })
            ->addColumn('action', function ($deposit) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_deposit')) ? '<a href="' . url(Config::get('adminPrefix') . '/deposits/edit/' . $deposit->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['user_id', 'total', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = Deposit::with(['user:id,first_name,last_name', 'currency:id,code,symbol'])->where(['deposits.agent_id' => $this->agent_id, 'payment_method_id' => Cash])->select('deposits.id', 'uuid', 'amount', 'charge_fixed', 'charge_percentage', 'deposits.status', 'user_id', 'currency_id', 'deposits.created_at');
        
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'deposits.id', 'searchable' => false, 'visible' => false])
        ->addColumn(['data' => 'created_at', 'name' => 'deposits.created_at', 'title' => __('Date')])
        ->addColumn(['data' => 'uuid', 'name' => 'deposits.uuid', 'title' => __('Unique ID'), 'visible' => false])
        ->addColumn(['data' => 'user_id', 'name' => 'user.first_name', 'visible' => false])
        ->addColumn(['data' => 'user_id', 'name' => 'user.last_name', 'title' => __('User')])
        ->addColumn(['data' => 'amount', 'name' => 'deposits.amount', 'title' => __('Amount')])
        ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => __('Fees')])
        ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total')])
        ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => __('Currency')])
        ->addColumn(['data' => 'status', 'name' => 'deposits.status', 'title' => __('Status')])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
        ->parameters(dataTableOptions());
    }
}
