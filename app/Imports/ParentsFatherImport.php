<?php

namespace App\Imports;

use App\Models\GeneralInformation;
use App\Models\StudentInformation;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class ParentsFatherImport implements ToModel
{
    public function model(array $row)
    {
        // Define how each row in the Excel file should be mapped to your database model

        $userCheck = User::where('email', $row[3])->first();
        if (empty($userCheck)) {
            $user = new User([
                'mobile' => $row[6],
                'email' => $row[3],
                'password' => 'Aa16001600',
            ]);
            $user->save();
            $user->assignRole('Parent(Father)');

            $generalInformation=new GeneralInformation([
                'user_id' => $user->id,
                'first_name_en' => $row[1],
                'last_name_en' => $row[2],
                'nationality' => $row[4],
                'gender' => 'Male',
                'passport_number' => $row[5],
            ]);
            $generalInformation->save();
        }
        $user = User::where('email', $row[3])->first();
        $studentInformation = StudentInformation::where('student_id', $row[0])->firstOrCreate([
            'student_id' => $row[0],
            'guardian' => $user->id,
            'parent_father_id' => $user->id,
        ]);

    }
}
