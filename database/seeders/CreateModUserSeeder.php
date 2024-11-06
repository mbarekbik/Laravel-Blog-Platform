<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateModUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'firstname' => 'Mod',
            'lastname' => 'Moderator',
            'email' => 'mod@db.com',
            'image_path' => '/images/avatars/user.png',
            'password' => bcrypt('mod1234'),
        ]);

        $role = Role::create(['name' => 'Moderator']);

        $permissions = [
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            '11' => 11,
            '12' => 12,
            '13' => 13,
            '14' => 14,
            '15' => 15,
            '16' => 16,
            '17' => 17,
            '18' => 18,
            '19' => 19,
            '20' => 20,
            '21' => 21,
            '22' => 22,
        ];

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
