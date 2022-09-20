<?php

namespace Modules\Remittance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $permissions = [
            ['group' => 'Remittance', 'name' => 'manage_remittance', 'display_name' => 'Manage Remittance', 'description' => 'Manage Remittance', 'user_type' => 'User'],
            ['group' => 'Remittance', 'name' => 'edit_remittance', 'display_name' => 'Edit Remittance', 'description' => 'Edit Remittance', 'user_type' => 'Admin'],
            ['group' => 'Remittance', 'name' => 'view_remittance', 'display_name' => 'View Remittance', 'description' => 'View Remittance', 'user_type' => 'Admin'],
        ];
       
        \App\Models\Permission::insert($permissions);

        $adminPermissions = \App\Models\Permission::whereIn('group', ['Remittance'])->where('user_type', 'Admin')->get(['id', 'display_name']);

        foreach ($adminPermissions as $value) {
            if ($value->display_name == null) continue;
            $roleData[] = [
                'role_id' => 1,
                'permission_id' => $value->id,
            ];
        }

        $userPermissions = \App\Models\Permission::where(['group' => 'Remittance', 'user_type' => 'User'])->get(['id']);

        if (!empty($userPermissions)) {
            foreach ($userPermissions as $value) {
                $roleData[] = [
                    'role_id' => 2,
                    'permission_id' => $value->id,
                ];
                $roleData[] = [
                    'role_id' => 3,
                    'permission_id' => $value->id,
                ];
            }
        }
        
        DB::table('permission_role')->insert($roleData);
    }
}
