<?php

namespace Database\Seeders;

use App\Models\GeneralInformation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var User $user
         */
        $user = User::query()->create([
            'name' => 'Ali',
            'family' => 'Karimi',
            'mobile' => '+989398888226',
            'email' => 'test@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $generalInformation = GeneralInformation::create(
            [
                'user_id' => $user->id
            ]
        );
        $role = Role::where('name', 'Parent(Father)')->first();
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
