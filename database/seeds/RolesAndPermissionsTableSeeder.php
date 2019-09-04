<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $roles = [
            [
                'id'           => 1,
                'name'         => 'owner',
                'display_name' => 'Owner',
                'description'  => 'Has all privileges to manage merchant and change settings and delete merchant.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 2,
                'name'         => 'admin',
                'display_name' => 'Admin',
                'description'  => 'Has all privileges to manage merchant and change settings.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 3,
                'name'         => 'team_lead',
                'display_name' => 'Team Lead',
                'description'  => 'Team Lead can manage merchant, but doesn\'t able to change settings.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 4,
                'name'         => 'member',
                'display_name' => 'Member',
                'description'  => 'Member can manage chat, but has no ability to manage merchant data.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $permissions = [
            [
                'id'           => 1,
                'name'         => 'manage_settings',
                'display_name' => 'Manage Settings',
                'description'  => 'Manage merchant common settings.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 2,
                'name'         => 'manage_automation',
                'display_name' => 'Manage Automation',
                'description'  => 'Ability to change automation settings.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 3,
                'name'         => 'manage_contacts',
                'display_name' => 'Manage Contacts',
                'description'  => 'Ability to manage contacts(delete contact, export contact list).',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 4,
                'name'         => 'view_analytics',
                'display_name' => 'View Analytics',
                'description'  => 'Ability to see analytics.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 5,
                'name'         => 'manage_invoices',
                'display_name' => 'Manage Invoices',
                'description'  => 'Ability to manage invoices.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 6,
                'name'         => 'view_invoices',
                'display_name' => 'View Invoices',
                'description'  => 'Ability to see invoices.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 7,
                'name'         => 'manage_lists',
                'display_name' => 'Manage Contact Lists',
                'description'  => 'Ability to create and delete contact lists.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 8,
                'name'         => 'manage_template_messages',
                'display_name' => 'Manage Template Messages',
                'description'  => 'Ability to submit new template messages.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 9,
                'name'         => 'send_notifications',
                'display_name' => 'Send Notifications',
                'description'  => 'Ability to send notifications.',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        DB::table('roles')->insert($roles);
        DB::table('permissions')->insert($permissions);
    }
}
