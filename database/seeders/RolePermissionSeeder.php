<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = now();

        $permissions = [
            // Core profile + communication
            ["name" => "Manage Profile"],
            ["name" => "Manage Password"],
            ["name" => "Manage Chat"],

            // Team & email
            ["name" => "Create Team"],
            ["name" => "Delete Team"],
            ["name" => "Manage Team"],
            ["name" => "Manage Email"],

            // Becoming Sovereigns
            ["name" => "Manage Becoming Sovereigns"],
            ["name" => "Manage Becomeing Sovereigns"],
            ["name" => "View Becoming Sovereigns"],
            ["name" => "Upload Becoming Sovereigns"],
            ["name" => "Edit Becoming Sovereigns"],
            ["name" => "Delete Becoming Sovereigns"],
            ["name" => "Download Becoming Sovereigns"],

            // Becoming Christ Like
            ["name" => "Manage Becoming Christ Like"],
            ["name" => "View Becoming Christ Like"],
            ["name" => "Upload Becoming Christ Like"],
            ["name" => "Edit Becoming Christ Like"],
            ["name" => "Delete Becoming Christ Like"],
            ["name" => "Download Becoming Christ Like"],

            // Becoming a Leader
            ["name" => "Manage Becoming a Leader"],
            ["name" => "View Becoming a Leader"],
            ["name" => "Upload Becoming a Leader"],
            ["name" => "Edit Becoming a Leader"],
            ["name" => "Delete Becoming a Leader"],
            ["name" => "Download Becoming a Leader"],

            // File management
            ["name" => "Upload File"],
            ["name" => "Delete File"],
            ["name" => "View File"],
            ["name" => "Edit File"],
            ["name" => "Manage File"],

            // Bulletin
            ["name" => "Manage Bulletin"],
            ["name" => "Edit Bulletin"],
            ["name" => "Create Bulletin"],
            ["name" => "Delete Bulletin"],

            // Job postings
            ["name" => "Manage Job Postings"],
            ["name" => "View Job Postings"],
            ["name" => "Create Job Postings"],
            ["name" => "Edit Job Postings"],
            ["name" => "Delete Job Postings"],

            // Meeting schedule
            ["name" => "Manage Meeting Schedule"],
            ["name" => "View Meeting Schedule"],
            ["name" => "Create Meeting Schedule"],
            ["name" => "Edit Meeting Schedule"],
            ["name" => "Delete Meeting Schedule"],

            // Events
            ["name" => "Manage Event"],
            ["name" => "Create Event"],
            ["name" => "Edit Event"],

            // Partners
            ["name" => "Create Partners"],
            ["name" => "Edit Partners"],
            ["name" => "Delete Partners"],
            ["name" => "Manage Partners"],
            ["name" => "View Partners"],

            // Help
            ["name" => "Manage Help"],

            // Admin panel permissions
            ["name" => "Manage My Profile"],
            ["name" => "Manage My Password"],
            ["name" => "Create Admin List"],
            ["name" => "Delete Admin List"],
            ["name" => "Manage Admin List"],
            ["name" => "Edit Admin List"],
            ["name" => "Manage Donations"],
            ["name" => "Manage Contact Us Messages"],
            ["name" => "Delete Contact Us Messages"],
            ["name" => "Manage Newsletters"],
            ["name" => "Delete Newsletters"],
            ["name" => "Create Testimonials"],
            ["name" => "Delete Testimonials"],
            ["name" => "Manage Testimonials"],
            ["name" => "Edit Testimonials"],
            ["name" => "Create Our Governance"],
            ["name" => "Delete Our Governance"],
            ["name" => "Manage Our Governance"],
            ["name" => "Edit Our Governance"],
            ["name" => "Create Our Organization"],
            ["name" => "Delete Our Organization"],
            ["name" => "Manage Our Organization"],
            ["name" => "Edit Our Organization"],
            ["name" => "Create Organization Center"],
            ["name" => "Delete Organization Center"],
            ["name" => "Manage Organization Center"],
            ["name" => "Edit Organization Center"],
            ["name" => "Manage Services"],
            ["name" => "Manage Pages"],

            // Frontend CMS + site pages
            ["name" => "Manage Home Page"],
            ["name" => "Manage Details Page"],
            ["name" => "Manage Organizations Page"],
            ["name" => "Manage About Us Page"],
            ["name" => "Manage Faq"],
            ["name" => "Create Faq"],
            ["name" => "Edit Faq"],
            ["name" => "Delete Faq"],
            ["name" => "Manage Gallery"],
            ["name" => "Create Gallery"],
            ["name" => "Edit Gallery"],
            ["name" => "Delete Gallery"],
            ["name" => "Manage Ecclesia Association Page"],
            ["name" => "Manage Principle and Business Page"],
            ["name" => "Manage Contact Us Page"],
            ["name" => "Manage Article of Association Page"],
            ["name" => "Manage Footer"],
            ["name" => "Manage Register Page Agreement Page"],
            ["name" => "Manage Member Privacy Policy Page"],
            ["name" => "Manage PMA Terms Page"],
            ["name" => "Manage Members Access"],
            ["name" => "Manage All Members"],
            ["name" => "Manage All Users"],
            ["name" => "Create All Users"],
            ["name" => "Edit All Users"],
            ["name" => "Delete All Users"],
            ["name" => "Manage Warehouse Manager"],
            ["name" => "Manage Assigned Warehouses"],

            // Site management
            ["name" => "Manage Privacy Policy Page"],
            ["name" => "Manage Terms and Conditions Page"],
            ["name" => "Manage Site Settings"],
            ["name" => "Manage Menu Settings"],

            // Countries
            ["name" => "Manage Countries"],

            // Strategy
            ["name" => "Manage Strategy"],
            ["name" => "Upload Strategy"],
            ["name" => "Download Strategy"],
            ["name" => "View Strategy"],
            ["name" => "Delete Strategy"],

            // Policy
            ["name" => "Manage Policy"],
            ["name" => "Upload Policy"],
            ["name" => "Download Policy"],
            ["name" => "View Policy"],
            ["name" => "Delete Policy"],

            // User activity
            ["name" => "Manage User Activity"],
            ["name" => "View User Activity"],
            ["name" => "Create User Activity"],
            ["name" => "Edit User Activity"],
            ["name" => "Delete User Activity"],

            // Topic
            ["name" => "Manage Topic"],
            ["name" => "Edit Topic"],
            ["name" => "Create Topic"],
            ["name" => "Delete Topic"],

            // Private Collaboration
            ["name" => "Manage Private Collaboration"],
            ["name" => "View Private Collaboration"],
            ["name" => "Create Private Collaboration"],
            ["name" => "Edit Private Collaboration"],
            ["name" => "Delete Private Collaboration"],

            // Elearning
            ["name" => "Manage Elearning CMS"],
            ["name" => "View Elearning CMS"],
            ["name" => "Create Elearning CMS"],
            ["name" => "Edit Elearning CMS"],
            ["name" => "Delete Elearning CMS"],
            ["name" => "Manage Elearning Category"],
            ["name" => "View Elearning Category"],
            ["name" => "Create Elearning Category"],
            ["name" => "Edit Elearning Category"],
            ["name" => "Delete Elearning Category"],
            ["name" => "Manage Elearning Product"],
            ["name" => "View Elearning Product"],
            ["name" => "Create Elearning Product"],
            ["name" => "Edit Elearning Product"],
            ["name" => "Delete Elearning Product"],
            ["name" => "Manage Elearning Topic"],
            ["name" => "View Elearning Topic"],
            ["name" => "Create Elearning Topic"],
            ["name" => "Edit Elearning Topic"],
            ["name" => "Delete Elearning Topic"],

            // Estore
            ["name" => "Manage Estore CMS"],
            ["name" => "View Estore CMS"],
            ["name" => "Create Estore CMS"],
            ["name" => "Edit Estore CMS"],
            ["name" => "Delete Estore CMS"],
            ["name" => "Manage Estore Users"],
            ["name" => "View Estore Users"],
            ["name" => "Manage Estore Category"],
            ["name" => "View Estore Category"],
            ["name" => "Create Estore Category"],
            ["name" => "Edit Estore Category"],
            ["name" => "Delete Estore Category"],
            ["name" => "Manage Estore Sizes"],
            ["name" => "View Estore Sizes"],
            ["name" => "Create Estore Sizes"],
            ["name" => "Edit Estore Sizes"],
            ["name" => "Manage Estore Colors"],
            ["name" => "View Estore Colors"],
            ["name" => "Create Estore Colors"],
            ["name" => "Edit Estore Colors"],
            ["name" => "Manage Estore Products"],
            ["name" => "View Estore Products"],
            ["name" => "Create Estore Products"],
            ["name" => "Edit Estore Products"],
            ["name" => "Delete Estore Products"],
            ["name" => "Manage Estore Settings"],
            ["name" => "View Estore Settings"],
            ["name" => "Edit Estore Settings"],
            ["name" => "Manage Estore Warehouse"],
            ["name" => "View Estore Warehouse"],
            ["name" => "Create Estore Warehouse"],
            ["name" => "Edit Estore Warehouse"],
            ["name" => "Delete Estore Warehouse"],
            ["name" => "Manage Estore Orders"],
            ["name" => "View Estore Orders"],
            ["name" => "Edit Estore Orders"],

            // Membership
            ["name" => "Manage Membership"],
            ["name" => "View Membership"],
            ["name" => "Create Membership"],
            ["name" => "Edit Membership"],
            ["name" => "Delete Membership"],
            ["name" => "Manage Membership Settings"],
            ["name" => "View Membership Settings"],
            ["name" => "Edit Membership Settings"],
            ["name" => "Manage Membership Members"],
            ["name" => "View Membership Members"],
            ["name" => "Manage Membership Payments"],
            ["name" => "View Membership Payments"],

            // Order status + templates
            ["name" => "Manage Order Status"],
            ["name" => "Create Order Status"],
            ["name" => "Edit Order Status"],
            ["name" => "Delete Order Status"],
            ["name" => "Manage Email Template"],
            ["name" => "Create Email Template"],
            ["name" => "Edit Email Template"],
            ["name" => "Delete Email Template"],

            // Chatbot
            ["name" => "Manage Chatbot"],
            ["name" => "View Chatbot History"],
            ["name" => "Manage Chatbot Keywords"],
            ["name" => "View Chatbot Analytics"],
        ];

        $permissionNames = [];
        foreach ($permissions as $permission) {
            $permissionData = [
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'] ?? 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (array_key_exists('type', $permission)) {
                $permissionData['type'] = $permission['type'];
            }

            $record = Permission::firstOrCreate(
                ['name' => $permissionData['name'], 'guard_name' => $permissionData['guard_name']],
                $permissionData
            );

            $permissionNames[] = $record->name;
        }

        $permissionNames = array_values(array_unique($permissionNames));

        $roles = [
            'SUPER ADMIN',
            'ADMINISTRATOR',
            'MEMBER_NON_SOVEREIGN',
            'LEADER',
            'ECCLESIA',
            'WAREHOUSE_ADMIN',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::where('name', 'SUPER ADMIN')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissionNames);
        }

        $administratorRole = Role::where('name', 'ADMINISTRATOR')->first();
        if ($administratorRole) {
            $administratorRole->givePermissionTo($permissionNames);
        }

        $memberPermissions = [
            'Manage Profile',
            'Manage Password',
        ];


        $memberRole = Role::where('name', 'MEMBER_NON_SOVEREIGN')->first();
        if ($memberRole) {
            $memberRole->givePermissionTo($memberPermissions);
        }

        $eccRole = Role::where('name', 'ECCLESIA')->first();
        if ($eccRole) {
            $eccRole->givePermissionTo(['Manage All Members']);
        }

        $warehouseAdminRole = Role::where('name', 'WAREHOUSE_ADMIN')->first();
        if ($warehouseAdminRole) {
            $warehouseAdminRole->givePermissionTo([
                'Manage Profile',
                'Manage Password',
                'Manage Warehouse Manager',
                'Manage Assigned Warehouses',
            ]);
        }
    }
}
