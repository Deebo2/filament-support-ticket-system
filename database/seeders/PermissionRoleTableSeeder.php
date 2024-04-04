<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_permissions = Permission::all();
        $agent_permissions = Permission::whereIn("title",[
            "category_show",
            "category_access",
            "label_show",
            "label_access",
            "ticket_show",
            "ticket_access"

        ])->get();
        Role::findOrFail(1)->Permissions()->sync($admin_permissions);
        Role::findOrFail(2)->Permissions()->sync($agent_permissions);
    }
}
