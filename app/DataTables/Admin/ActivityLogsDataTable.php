<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use Session, Config, Auth;
use Yajra\DataTables\Services\DataTable;

class ActivityLogsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($activityLog) {
                return dateFormat($activityLog->created_at);
            })
            ->addColumn('username', function ($activityLog) {
                if ($activityLog->type == 'Admin') {
                    $admin = (isset($activityLog->admin->id) && !empty($activityLog->admin->first_name)) ? $activityLog->admin->first_name . ' ' . $activityLog->admin->last_name : '-';
                    $withLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_admin')) ? '<a href="' . url(Config::get('adminPrefix') . '/admin-user/edit/' . $activityLog->admin->id) . '">' . $admin . '</a>' : $admin;
                } elseif (module('Agent') && $activityLog->type == 'Agent') {
                    $user = isset($activityLog->agent) ? optional($activityLog->agent)->first_name.' '. optional($activityLog->agent)->last_name : '-';
                    $url = isActive('Agent') ? '<a href="' . url(Config::get('adminPrefix') . '/agents/edit/' . $activityLog->user_id) . '">'.$user.'</a>' : $user;
                    $withLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_agent')) ? $url : $user;
                } else {
                    $user = (isset($activityLog->user->id) && !empty($activityLog->user->first_name)) ? $activityLog->user->first_name . ' ' . $activityLog->user->last_name : '-';
                    $withLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $activityLog->user->id) . '">' . $user . '</a>' : $user;
                }
                return $withLink;
            })
            ->editColumn('browser_agent', function ($activityLog) {
                $getBrowser = getBrowser($activityLog->browser_agent);
                return $getBrowser['name'] . ' ' . substr($getBrowser['version'], 0, 4) . ' | ' . ucfirst($getBrowser['platform']);
            })
            ->rawColumns(['user_id', 'username'])
            ->make(true);
    }

    public function query()
    {
        $query = ActivityLog::with([
            'user'   => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
            'admin' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
        ])
        ->select('activity_logs.*');
        
        if (module('Agent')) {
            $query = $query->with('agent:id,first_name,last_name');
        }
        
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'activity_logs.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'activity_logs.created_at', 'title' => 'Date'])
            ->addColumn(['data' => 'type', 'name' => 'activity_logs.type', 'title' => 'User Type'])
            ->addColumn(['data' => 'username', 'name' => 'user.last_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'user.first_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'admin.last_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'admin.first_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'agent.last_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'agent.first_name', 'title' => 'User', 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'username', 'title' => 'Username'])
            ->addColumn(['data' => 'ip_address', 'name' => 'activity_logs.ip_address', 'title' => 'IP Address'])
            ->addColumn(['data' => 'browser_agent', 'name' => 'activity_logs.browser_agent', 'title' => 'Browser | Platform'])
            ->parameters(dataTableOptions());
    }
}
