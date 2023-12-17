<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permission Types
         *
         */
        $Permissionitems = [
            [
                'name'        => 'Can View Alive Clan Members',
                'slug'        => 'view.alive.clan.members',
                'description' => 'Can view alive clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Registered Clan Members',
                'slug'        => 'view.registered.clan.members',
                'description' => 'Can view registered clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Age Statistics',
                'slug'        => 'view.age.statistics',
                'description' => 'Can view age statistics',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Children Statistics',
                'slug'        => 'view.children.statistics',
                'description' => 'Can view children statistics',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Youths Statistics',
                'slug'        => 'view.youths.statistics',
                'description' => 'Can view youths statistics',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Adults Statistics',
                'slug'        => 'view.adults.statistics',
                'description' => 'Can view adults statistics',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Clan Members',
                'slug'        => 'view.clan.members',
                'description' => 'Can view clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Clan Members',
                'slug'        => 'create.clan.members',
                'description' => 'Can create clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Clan Members',
                'slug'        => 'edit.clan.members',
                'description' => 'Can edit details of clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Change Clan Member Parents',
                'slug'        => 'change.clan.member.parents',
                'description' => 'Can change clan member parents',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Clan Members',
                'slug'        => 'delete.clan.members',
                'description' => 'Can delete details of clan members',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Add Clan Member Spouses',
                'slug'        => 'add.clan.member.spouse',
                'description' => 'Can add details of clan member spouse',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Remove Clan Member Spouses',
                'slug'        => 'remove.clan.member.spouse',
                'description' => 'Can remove details of clan member spouse',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Total Users',
                'slug'        => 'view.total.users',
                'description' => 'Can view total users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Total Users with Deails',
                'slug'        => 'view.total.users.with.details',
                'description' => 'Can view total users with deails',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Users',
                'slug'        => 'view.users',
                'description' => 'Can view users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Users',
                'slug'        => 'create.users',
                'description' => 'Can create new users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Users',
                'slug'        => 'delete.users',
                'description' => 'Can delete users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Change Password',
                'slug'        => 'change.password',
                'description' => 'Can change password',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Clean-up User Records',
                'slug'        => 'clean.up.user.records',
                'description' => 'Can clean-up user records',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Assign Roles',
                'slug'        => 'assign.roles.to.user',
                'description' => 'Can assign roles to user',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Remove Roles',
                'slug'        => 'remove.roles.from.user',
                'description' => 'Can remove roles from user',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Roles',
                'slug'        => 'view.roles',
                'description' => 'Can view roles',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Permissions',
                'slug'        => 'view.permissions',
                'description' => 'Can view permissions',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Roles',
                'slug'        => 'create.roles',
                'description' => 'Can create new roles',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Roles',
                'slug'        => 'edit.roles',
                'description' => 'Can edit details of roles',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Add Permissions to Roles',
                'slug'        => 'add.permissions.to.roles',
                'description' => 'Can add permissions to roles',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Remove Permissions from Roles',
                'slug'        => 'remove.permissions.from.roles',
                'description' => 'Can remove permissions from roles',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Roles',
                'slug'        => 'delete.roles',
                'description' => 'Can delete roles',
                'model'       => 'Permission',
            ],
        ];

        /*
         * Add Permission Items
         *
         */
        foreach ($Permissionitems as $Permissionitem) {
            $newPermissionitem = config('roles.models.permission')::where('slug', '=', $Permissionitem['slug'])->first();
            if ($newPermissionitem === null) {
                $newPermissionitem = config('roles.models.permission')::create([
                    'name'          => $Permissionitem['name'],
                    'slug'          => $Permissionitem['slug'],
                    'description'   => $Permissionitem['description'],
                    'model'         => $Permissionitem['model'],
                ]);
            }
        }
    }
}
