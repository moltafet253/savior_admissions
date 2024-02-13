<?php

namespace Database\Seeders;

use App\Models\GeneralInformation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FinancialManagerSeeder extends Seeder
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
            'mobile' => '+989026548798',
            'email' => 'test@testi.com',
            'password' => bcrypt(12345678),
        ]);
        $generalInformation = GeneralInformation::create(
            [
                'user_id' => $user->id,
                'first_name_fa' => 'امیر',
                'last_name_fa' => 'مدیر مالی',
                'first_name_en' => 'Amir',
                'last_name_en' => 'Finance',
            ]
        );
        $role = Role::where('name', 'Financial Manager')->first();
        $user->assignRole([$role->id]);
    }
}
