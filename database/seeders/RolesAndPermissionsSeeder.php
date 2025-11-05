<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Puhasta permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //õiguste loomine
        $permissions = [
            'posts.create',
            'posts.update.own',
            'posts.update.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'comments.create',
            'comments.moderate',
            'users.manage',
            'settings.manage',
            'categories.manage',
            'tags.manage',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        //Rollid: Admin, Moderaator, Autor
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $moderator = Role::firstOrCreate(['name' => 'Moderator']);
        $author = Role::firstOrCreate(['name' => 'Author']);

        //Rollidele õiguste määramine
        $admin->syncPermission(Permission::all());

        $moderator -> syncPermission ([
            'posts.create',
            'posts.update.own',
            'posts.update.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'comments.create',
            'comments.moderate',
            'categories.manage',
            'tags.manage',
        ]);

        $author -> syncPermission ([
            'posts.create',
            'posts.update.own',
            'posts.delete.own',
            'comments.create',
        ]);

        //Näidis kasutajad Admin, Moderaator, Autor
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password123')]
        );
        $adminUser->assignRole($admin); // Lisab õigused

        $modUser = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            ['name' => 'Moderator', 'password' => Hash::make('password123')]
            
        );
        $modUser->assignRole($moderator);

        $authorUser = User::firstOrCreate(
            ['email' => 'author@example.com'],
            ['name' => 'Author', 'password' => Hash::make('password123')]
        );
        $authorUser->assignRole($author);

        //Uunda Cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

    }
}
