<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\MerchantGroup;
use Yajra\DataTables\Services\DataTable;
use Session, Config, Auth;

class MerchantGroupsDataTable extends DataTable
{

    public function ajax()
    {
        $role = $this->query();

        return datatables()
            ->of($role)
            ->addColumn('name', function ($merchantGroup) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_merchant_group')) ? '<a href="' . url(Config::get('adminPrefix') . '/settings/edit-merchant-group/' . $merchantGroup->id) . '">' . ucfirst($merchantGroup->name) . '</a>' : ucfirst($merchantGroup->name);
            })
            ->editColumn('description', function ($merchantGroup) {
                return ucfirst($merchantGroup->description);
            })
            ->editColumn('fee', function ($merchantGroup) {
                return formatNumber($merchantGroup->fee);
            })
            ->editColumn('is_default', function ($merchantGroup) {
                return isDefault($merchantGroup->is_default);
            })
            ->addColumn('action', function ($merchantGroup) {
                $edit = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_merchant_group')) ? '<a href="' . url(Config::get('adminPrefix') . '/settings/edit-merchant-group/' . $merchantGroup->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (Common::has_permission(Auth::guard('admin')->user()->id, 'delete_merchant_group')) ? '<a href="' . url(Config::get('adminPrefix') . '/settings/delete-merchant-group/' . $merchantGroup->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>' : '';

                return $edit . $delete;
            })
            ->rawColumns(['name', 'is_default', 'action'])
            ->make(true);
    }

    public function query()
    {
        $merchantGroup = MerchantGroup::select();
        return $this->applyScopes($merchantGroup);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'merchant_groups.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'merchant_groups.name', 'title' => 'Name'])
            ->addColumn(['data' => 'description', 'name' => 'merchant_groups.description', 'title' => 'Description'])
            ->addColumn(['data' => 'fee', 'name' => 'merchant_groups.fee', 'title' => 'Fee (%)'])
            ->addColumn(['data' => 'is_default', 'name' => 'merchant_groups.is_default', 'title' => 'Default'])
            ->addColumn(['data'  => 'action', 'name'  => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
