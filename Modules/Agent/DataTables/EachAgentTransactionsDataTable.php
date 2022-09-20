<?php

namespace Modules\Agent\DataTables;

use App\Models\Transaction;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\{Auth, 
    Config
};
use Yajra\DataTables\Services\DataTable;

class EachAgentTransactionsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
        ->eloquent($this->query())
        ->editColumn('created_at', function ($transaction) {
            return dateFormat($transaction->created_at);
        })
        ->addColumn('sender', function ($transaction) {
            $senderWithLink = '-';
            switch ($transaction->transaction_type_id) {
                case Deposit:
                case Withdrawal:
                    if (isset($transaction->user->first_name) && !empty($transaction->user->first_name)) {
                        $sender = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                        $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $sender . '</a>' : $sender;
                    } elseif (module('Agent') && empty($transaction->user_id) && !empty($transaction->agent_id)) {
                        $sender = $transaction->agent->first_name . ' ' . $transaction->agent->last_name;
                        $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/edit/' . $transaction->agent_id) . '">' . $sender . '</a> (Agent)': $sender;
                    }
                break;
            }
            return $senderWithLink;
        })
        ->addColumn('receiver', function ($transaction) {
            $receiverWithLink = '-';
            switch ($transaction->transaction_type_id) {
                case Deposit:
                case Withdrawal:
                    if (!empty($transaction->end_user)) {
                        $receiver = $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name;
                        $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->end_user_id) . '">' . $receiver . '</a>' : $receiver;
                    }
                break;
            }
            return $receiverWithLink;
        })
        ->editColumn('transaction_type_id', function ($transaction) {
            return isset($transaction->transaction_type) ? str_replace('_', ' ', $transaction->transaction_type->name) : '';
        })
        ->editColumn('subtotal', function ($transaction) {
            return isset($transaction->currency_id) && !empty($transaction->subtotal) ? formatNumber($transaction->subtotal, $transaction->currency_id) : '';
        })
        ->addColumn('fees', function ($transaction) {
            return (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0) ? '-' : formatNumber($transaction->charge_percentage + $transaction->charge_fixed, $transaction->currency_id));
        })
        ->editColumn('total', function ($transaction) {
            $total = isset($transaction->currency_id) ? formatNumber($transaction->total, $transaction->currency_id) : '';
            return '<td><span class="text-' . (($transaction->total > 0) ? 'green' : 'red') . '">' . $total . '</span></td>';
        })
        ->editColumn('currency_id', function ($transaction) {
            return isset($transaction->currency->code) ? $transaction->currency->code : '';
        })
        ->editColumn('status', function ($transaction) {
            return getStatusLabel($transaction->status);
        })
        ->addColumn('action', function ($transaction) {
            return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_transaction')) ? '<a href="' . url(Config::get('adminPrefix') . '/transactions/edit/' . $transaction->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
        })
        ->rawColumns(['sender', 'receiver', 'total', 'status', 'action'])
        ->make(true);
    }

    public function query()
    {
        $agent    = $this->agent_id;

        $status   = isset(request()->status) ? request()->status : 'all';
        $currency = isset(request()->currency) ? request()->currency : 'all';
        $type     = isset(request()->type) ? request()->type : 'all';
        $from     = isset(request()->from) && !empty((request()->from)) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) && !empty((request()->to)) ? setDateForDb(request()->to) : null;

        $query    = (new Transaction())->getEachUserTransactionsList($from, $to, $status, $currency, $type, $agent);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'transactions.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
        ->addColumn(['data' => 'uuid', 'name' => 'transactions.uuid', 'visible' => false])
        ->addColumn(['data' => 'created_at', 'name' => 'transactions.created_at', 'title' => __('Date')])
        ->addColumn(['data' => 'sender', 'name' => 'user.last_name', 'visible' => false])
        ->addColumn(['data' => 'sender', 'name' => 'user.first_name', 'title' => __('User')])
        ->addColumn(['data' => 'transaction_type_id', 'name' => 'transaction_type.name', 'title' => __('Type')])
        ->addColumn(['data' => 'subtotal', 'name' => 'transactions.subtotal', 'title' => __('Amount')])
        ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => __('Fees')])
        ->addColumn(['data' => 'total', 'name' => 'transactions.total', 'title' => __('Total')])
        ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => __('Currency')])
        ->addColumn(['data' => 'receiver', 'name' => 'end_user.last_name', 'visible' => false])
        ->addColumn(['data' => 'receiver', 'name' => 'end_user.first_name', 'title' => __('Receiver')])
        ->addColumn(['data' => 'status', 'name' => 'transactions.status', 'title' => __('Status')])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
        ->parameters(dataTableOptions());
    }
}
