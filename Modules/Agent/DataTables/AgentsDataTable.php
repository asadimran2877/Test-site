<?php

namespace Modules\Agent\DataTables;

use App\Http\Helpers\Common;
use Modules\Agent\Entities\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Services\DataTable;

class AgentsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
        ->eloquent($this->query())
        ->editColumn('first_name', function ($agent) {
            return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/edit/' . $agent->id) . '">' . $agent->first_name . '</a>' : $agent->first_name;
        })
        ->editColumn('last_name', function ($agent) {
            return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/edit/' . $agent->id) . '">' . $agent->last_name . '</a>' : $agent->last_name;
        })
        ->editColumn('formattedPhone', function ($agent) {
            return $agent->formattedPhone ?? '-';
        })
        ->editColumn('status', function ($agent) {
            return getStatusLabel($agent->status);
        })
        ->editColumn('created_at', function ($agent) {
            return dateFormat($agent->created_at) ?? '-';
        })
        ->addColumn('action', function ($agent) {
            $view = (Common::has_permission(Auth::guard('admin')->user()->id, 'view_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/details/' . $agent->id) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;' : '';
            $edit = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/edit/' . $agent->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            if ($agent->status == 'Inactive') {
                $delete = (Common::has_permission(Auth::guard('admin')->user()->id, 'delete_agent')) ? '<a disabled class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i></a>' : '';
            } else {
                $delete = (Common::has_permission(Auth::guard('admin')->user()->id, 'delete_agent')) ? '<a href="' . url(Config::get('adminPrefix') . '/agents/delete/' . $agent->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>' : '';
            }

            return $view . $edit . $delete;
        })
        ->rawColumns(['first_name', 'last_name', 'status', 'action'])
        ->make(true);
    }

    public function query()
    {
        $query = Agent::select('id', 'first_name', 'last_name', 'formattedPhone', 'email', 'status', 'created_at');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'agents.id', 'searchable' => false, 'visible' => false])
        ->addColumn(['data' => 'first_name', 'name' => 'agents.first_name', 'title' => __('First Name')])
        ->addColumn(['data' => 'last_name', 'name' => 'agents.last_name', 'title' => __('Last Name')])
        ->addColumn(['data' => 'formattedPhone', 'name' => 'agents.formattedPhone', 'title' => __('Phone')])
        ->addColumn(['data' => 'email', 'name' => 'agents.email', 'title' => __('Email')])
        ->addColumn(['data' => 'created_at', 'name' => 'agents.created_at', 'title' => __('Created at')])
        ->addColumn(['data' => 'status', 'name' => 'agents.status', 'title' => __('Status')])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
        ->parameters(dataTableOptions());
    }
}
