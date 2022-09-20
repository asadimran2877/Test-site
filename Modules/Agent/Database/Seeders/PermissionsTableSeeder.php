<?php

namespace Modules\Agent\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
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
    
            ['group' => 'Agent', 'name' => 'view_agent', 'display_name' => 'View Agent', 'description' => 'View Agent', 'user_type' => 'Admin'],
            ['group' => 'Agent', 'name' => 'add_agent', 'display_name' => 'Add Agent', 'description' => 'Add Agent', 'user_type' => 'Admin'],
            ['group' => 'Agent', 'name' => 'edit_agent', 'display_name' => 'Edit Agent', 'description' => 'Edit Agent', 'user_type' => 'Admin'],
            ['group' => 'Agent', 'name' => 'delete_agent', 'display_name' => 'Delete Agent', 'description' => 'Delete Agent', 'user_type' => 'Admin'],
        ];

        \App\Models\Permission::insert($permissions);

        $adminPermissions = \App\Models\Permission::whereIn('group', ['Agent'])->where('user_type', 'Admin')->get(['id', 'display_name']);

        foreach ($adminPermissions as $value) {
            if ($value->display_name == null) continue;
            $roleData[] = [
                'role_id' => 1,
                'permission_id' => $value->id,
            ];
        }

        $agentPermissions = \App\Models\Permission::where(['group' => 'Agent', 'user_type' => 'Agent'])->get(['id']);

        if (!empty($agentPermissions)) {
            foreach ($agentPermissions as $value) {
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
