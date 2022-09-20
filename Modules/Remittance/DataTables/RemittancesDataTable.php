<?php

namespace Modules\Remittance\DataTables;

use Auth;
use Config;
use Modules\Remittance\Entities\Remittance;
use App\Http\Helpers\Common;
use Yajra\DataTables\Services\DataTable;

class RemittancesDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($remittance) {
                return dateFormat($remittance->created_at);
            })
            ->addColumn('sender', function ($remittance) {
                $sender = isset($remittance->sender) ? $remittance->sender->first_name . ' ' . $remittance->sender->last_name : "-";

                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $remittance->sender->id) . '">' . $sender . '</a>' : $sender;
            })
            ->editColumn('transferred_amount', function ($remittance) {
                return formatNumber($remittance->transferred_amount);
            })

            ->editColumn('transferred_currency_id', function ($remittance) {
                return $remittance->currency->code;
            })

            ->editColumn('fees', function ($remittance) {
                return ($remittance->fees == 0) ? '-' : formatNumber($remittance->fees);
            })
            ->addColumn('total', function ($remittance) {
                if ($remittance->total) {
                    $total = '<td><span class="text-red">-' . formatNumber($remittance->total) . '</span></td>';
                }
                return $total ?? '-';
            })

            ->addColumn('received_amount', function ($remittance) {
                if ($remittance->received_amount) {
                    $received_amount = '<td><span class="text-green">+' . formatNumber($remittance->received_amount) . '</span></td>';
                }
                return $received_amount ?? '-';
            })
            ->editColumn('received_currency_id', function ($remittance) {
                return $remittance->rcvCurrency->code;
            })
            ->addColumn('recipent', function ($remittance) {
                if (isset($remittance->recipent)) {
                    $recipentWithLink = $remittance->recipent->nick_name;
                } else {
                    if (!empty($remittance->email)) {
                        $recipentWithLink = $remittance->email;
                    } elseif (!empty($remittance->phone)) {
                        $recipentWithLink = $remittance->phone;
                    } else {
                        $recipentWithLink = '-';
                    }
                }
                return $recipentWithLink;
            })
            ->editColumn('status', function ($remittance) {
                return getStatusLabel($remittance->status);
            })
            ->addColumn('action', function ($remittance) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_remittance')) ?
                    '<a href="' . url(Config::get('adminPrefix') . '/remittances/edit/' . $remittance->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['sender', 'receiver', 'total', 'status', 'action', 'received_amount'])
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
        $query    = (new Remittance())->getRemittancesList($from, $to, $status, $currency, $pm, $user);
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'remittances.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])

            ->addColumn(['data' => 'uuid', 'name' => 'remittances.uuid', 'title' => __('UUID'), 'visible' => false])

            ->addColumn(['data' => 'sender', 'name' => 'sender.last_name', 'title' => __('Last Name'), 'visible' => false])

            ->addColumn(['data' => 'recipent', 'name' => 'recipent.last_name', 'title' => __('Last Name'), 'visible' => false])

            ->addColumn(['data' => 'created_at', 'name' => 'remittances.created_at', 'title' => __('Date')])

            ->addColumn(['data' => 'sender', 'name' => 'sender.first_name', 'title' => __('Send In')])

            ->addColumn(['data' => 'transferred_amount', 'name' => 'remittances.transferred_amount', 'title' => __('Send Amount')])

            ->addColumn(['data' => 'fees', 'name' => 'remittances.fees', 'title' => __('Fees')])

            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total'), 'searchable' => false])

            ->addColumn(['data' => 'transferred_currency_id', 'name' => 'currency.code', 'title' => __('Send Currency')])

            ->addColumn(['data' => 'received_amount', 'name' => 'remittances.received_amount', 'title' => __('Rcv Amount')])

            ->addColumn(['data' => 'received_currency_id', 'name' => 'currency.code', 'title' => __('Rcv Currency')])

            ->addColumn(['data' => 'recipent', 'name' => 'recipent.first_name', 'title' => __('Receive In')])

            ->addColumn(['data' => 'status', 'name' => 'remittances.status', 'title' => __('Status')])

            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])

            ->parameters(dataTableOptions());
    }
}
