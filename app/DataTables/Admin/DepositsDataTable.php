<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Deposit;
use Yajra\DataTables\Services\DataTable;
use Session, Config, Auth;

class DepositsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($deposit) {
                return dateFormat($deposit->created_at);
            })
            ->addColumn('user_id', function ($deposit) {
                if (isset($deposit->user->id) && !empty($deposit->user->first_name) && Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) {
                    $sender = $deposit->user->first_name . ' ' . $deposit->user->last_name;
                    return '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $deposit->user->id) . '">' . $sender . '</a>';
                } elseif (module('Agent') && isset($deposit->agent->id) && !empty($deposit->agent->first_name) && Common::has_permission(Auth::guard('admin')->user()->id, 'edit_agent')) {
                    $sender = $deposit->agent->first_name.' '.$deposit->agent->last_name;
                    return isActive('Agent') ? '<a href="' . url(Config::get('adminPrefix').'/agents/edit/' . $deposit->agent->id) . '">'.$sender.'</a>' : $sender;
                }
                return '-';
            })
            ->editColumn('amount', function ($deposit) {
                return formatNumber($deposit->amount, $deposit->currency_id);
            })
            ->addColumn('fees', function ($deposit) {
                return ($deposit->charge_percentage == 0) && ($deposit->charge_fixed == 0) ? '-' : formatNumber($deposit->charge_percentage + $deposit->charge_fixed, $deposit->currency_id);
            })
            ->addColumn('total', function ($deposit) {
                return '<td><span class="text-' . ((($deposit->charge_percentage + $deposit->charge_fixed + $deposit->amount) > 0) ? 'green' : 'red') . '">+' . formatNumber($deposit->charge_percentage + $deposit->charge_fixed + $deposit->amount, $deposit->currency_id) . '</span></td>';
            })
            ->editColumn('currency_id', function ($deposit) {
                return (isset($deposit->currency->code) && !empty($deposit->currency->code)) ? $deposit->currency->code : '-';
            })
            ->editColumn('payment_method_id', function ($deposit) {
                if (isset($deposit->payment_method)) {
                    return ($deposit->payment_method->name == "Mts") ? settings('name') : $deposit->payment_method->name;
                }
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
        $status   = isset(request()->status) ? request()->status : 'all';
        $currency = isset(request()->currency) ? request()->currency : 'all';
        $pm       = isset(request()->payment_methods) ? request()->payment_methods : 'all';
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $query    = (new Deposit())->getDepositsList($from, $to, $status, $currency, $pm, $user);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'deposits.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'deposits.created_at', 'title' => 'Date'])
            ->addColumn(['data' => 'uuid', 'name' => 'deposits.uuid', 'title' => 'UUID', 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.last_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'user.first_name', 'title' => 'User'])
            ->addColumn(['data' => 'amount', 'name' => 'deposits.amount', 'title' => 'Amount'])
            ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => 'Fees'])
            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => 'Total'])
            ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => 'Currency'])
            ->addColumn(['data' => 'payment_method_id', 'name' => 'payment_method.name', 'title' => 'Payment Method'])
            ->addColumn(['data' => 'status', 'name' => 'deposits.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
