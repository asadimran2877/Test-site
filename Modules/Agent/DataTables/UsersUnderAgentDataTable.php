<?php

namespace Modules\Agent\DataTables;

use App\Models\User;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\{Auth, 
    Config
};
use Yajra\DataTables\Services\DataTable;

class UsersUnderAgentDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
        ->eloquent($this->query())
        ->editColumn('first_name', function ($user) {
            return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $user->id) . '">' . $user->first_name . '</a>' : $user->first_name;
        })
        ->editColumn('last_name', function ($user) {
            return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $user->id) . '">' . $user->last_name . '</a>' : $user->last_name;
        })
        ->editColumn('phone', function ($user) {
            return isset($user->phone) && !empty($user->phone) ? '+' . $user->carrierCode . $user->phone : '-';
        })
        ->editColumn('created_at', function ($user) {
            return dateFormat($user->created_at);
        })
        ->editColumn('status', function ($user) {
            return getStatusLabel($user->status);
        })
        ->addColumn('action', function ($user) {
            $edit = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $user->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            $delete = (Common::has_permission(Auth::guard('admin')->user()->id, 'delete_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/delete/' . $user->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>' : '';
            return $edit . $delete;
        })
        ->rawColumns(['first_name', 'last_name', 'status', 'action'])
        ->make(true);
    }

    public function query()
    {
        $query = User::where('agent_id', $this->agent_id)->select('id', 'first_name', 'last_name', 'carrierCode', 'phone', 'email', 'status', 'created_at');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'users.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
        ->addColumn(['data' => 'first_name', 'name' => 'users.first_name', 'title' => __('First Name')])
        ->addColumn(['data' => 'last_name', 'name' => 'users.last_name', 'title' => __('Last Name')])
        ->addColumn(['data' => 'email', 'name' => 'users.email', 'title' => __('Email')])
        ->addColumn(['data' => 'phone', 'name' => 'users.phone', 'title' => __('Phone')])
        ->addColumn(['data' => 'created_at', 'name' => 'users.created_at', 'title' => __('Created at')])
        ->addColumn(['data' => 'status', 'name' => 'users.status', 'title' => __('Status')])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
        ->parameters(dataTableOptions());
    }
}
