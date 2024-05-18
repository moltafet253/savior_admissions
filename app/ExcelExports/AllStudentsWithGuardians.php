<?php
namespace App\ExcelExports;

use App\Models\Branch\ApplicationReservation;
use App\Models\StudentInformation;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllStudentsWithGuardians implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return $students=StudentInformation::with('generalInformations')
            ->with('fatherInfo')
            ->with('motherInfo')
            ->with('nationalityInfo')
            ->with('guardianInfo')
            ->get()
            ->map(function ($student) {
                $applicationReservation = ApplicationReservation::where('payment_status', 1)
                    ->where('student_id', $student->student_id)
                    ->get();

                $applicationDetails = null;
                if ($applicationReservation->isNotEmpty()) {
                    $applicationDetails = $applicationReservation->first()->id;
                }
                return [
                    'Student id' => $student->student_id,
                    'Student Name' => $student->generalInformations->first_name_en,
                    'Student Family' => $student->generalInformations->last_name_en,
                    'Guardian id' => $student->guardian,
                    'Guardian Name' => $student->guardianInfo->generalInformationInfo->last_name_en,
                    'Guardian Family' => $student->guardianInfo->generalInformationInfo->last_name_en,
                    'Guardian Mobile' => @$student->guardianInfo->mobile,
                    'Application' => $applicationDetails
                    // Add more columns as needed
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Student id',
            'Student Name',
            'Student Family',
            'Guardian id',
            'Guardian Name',
            'Guardian Family',
            'Guardian Mobile',
            'Application',
            // Add more headings if needed
        ];
    }
}
