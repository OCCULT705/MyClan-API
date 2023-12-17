<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Role Types
         *
         */
        $RoleItems = [
            [
                'name'        => 'Administrator',
                'slug'        => 'administrator',
                'description' => 'Administrator Role',
                'level'       => 5,
            ],
            [
                'name'        => 'Senior Clan Member',
                'slug'        => 'senior.clan.member',
                'description' => 'Senior Clan Member Role',
                'level'       => 4,
            ],
            [
                'name'        => 'Clan Member',
                'slug'        => 'clan.member',
                'description' => 'Clan Member Role',
                'level'       => 0,
            ],
        ];

        /*
         * Add Role Items
         *
         */
        foreach ($RoleItems as $RoleItem) {
            $newRoleItem = config('roles.models.role')::where('slug', '=', $RoleItem['slug'])->first();
            if ($newRoleItem === null) {
                $newRoleItem = config('roles.models.role')::create([
                    'name'          => $RoleItem['name'],
                    'slug'          => $RoleItem['slug'],
                    'description'   => $RoleItem['description'],
                    'level'         => $RoleItem['level'],
                ]);
            }
        }
    }
}
