<?php

namespace Database\Seeders;

use App\Models\GeneralInformation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ParentMotherSeeder extends Seeder
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
            'name' => 'Fatima',
            'family' => 'Mother',
            'mobile' => '+989398844226',
            'email' => 'test34@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $generalInformation = GeneralInformation::create(
            [
                'user_id' => $user->id
            ]
        );
        $role = Role::where('name', 'Parent(Mother)')->first();
        $user->assignRole([$role->id]);
    }
}