<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Permissions
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'post-highlight',
            'post-super-list',
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'comment-list',
            'comment-edit',
            'comment-delete',
            'comment-super-list',
            'image-list',
            'image-delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
