<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define resources
        $resources = [
            'user',
            'role',
            'permission',
            'product',
            'category',
            'order',
            'warehouse',
            'tag',
            'post',
        ];

        // Define permission actions with descriptions
        $actions = [
            'view_any' => 'Can view the list of records',
            'view' => 'Can view individual record details',
            'create' => 'Can create new records',
            'update' => 'Can edit existing records',
            'delete' => 'Can delete records',
            'delete_any' => 'Can bulk delete multiple records',
            'restore' => 'Can restore soft-deleted records',
            'restore_any' => 'Can bulk restore multiple records',
            'force_delete' => 'Can permanently delete records',
            'force_delete_any' => 'Can bulk permanently delete records',
            'replicate' => 'Can duplicate/clone records',
            'reorder' => 'Can change the order of records',
        ];

        // Create permissions for each resource
        foreach ($resources as $resource) {
            foreach ($actions as $action => $description) {
                Permission::firstOrCreate(
                    [
                        'name' => "{$action}_{$resource}",
                        'guard_name' => 'web',
                    ],
                    [
                        'description' => $description,
                    ]
                );
            }
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

        // Super Admin gets all permissions (handled by Gate::before in AppServiceProvider)
        // No need to assign permissions explicitly

        // Admin gets most permissions except force delete and some sensitive operations
        $adminPermissions = Permission::where('name', 'not like', '%force_delete%')
            ->where('name', 'not like', '%permission')
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Manager gets management permissions for products, orders, warehouses
        $managerPermissions = [
            // Products
            'view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product',
            // Categories
            'view_any_category', 'view_category', 'create_category', 'update_category', 'delete_category',
            // Orders
            'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
            // Warehouses
            'view_any_warehouse', 'view_warehouse', 'create_warehouse', 'update_warehouse',
            // Tags
            'view_any_tag', 'view_tag', 'create_tag', 'update_tag', 'delete_tag',
            // Users (view only)
            'view_any_user', 'view_user',
        ];
        $manager->syncPermissions($managerPermissions);

        // Editor gets content management permissions
        $editorPermissions = [
            // Posts
            'view_any_post', 'view_post', 'create_post', 'update_post', 'delete_post',
            // Tags
            'view_any_tag', 'view_tag', 'create_tag', 'update_tag',
            // Categories
            'view_any_category', 'view_category', 'create_category', 'update_category',
            // Products (view only)
            'view_any_product', 'view_product',
        ];
        $editor->syncPermissions($editorPermissions);

        // Viewer gets only view permissions
        $viewerPermissions = Permission::where('name', 'like', 'view%')->get();
        $viewer->syncPermissions($viewerPermissions);

        // Create demo users
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $superAdminUser->assignRole($superAdmin);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole($admin);

        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
            ]
        );
        $managerUser->assignRole($manager);

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('password'),
            ]
        );
        $editorUser->assignRole($editor);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Demo users created:');
        $this->command->info('- superadmin@example.com / password (Super Admin)');
        $this->command->info('- admin@example.com / password (Admin)');
        $this->command->info('- manager@example.com / password (Manager)');
        $this->command->info('- editor@example.com / password (Editor)');
    }
}
