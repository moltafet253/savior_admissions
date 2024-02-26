<?php

namespace App\Http\Controllers\BranchInfo;

use App\Http\Controllers\Controller;
use App\Models\Catalogs\CurrentIdentificationType;
use App\Models\Country;
use App\Models\GeneralInformation;
use App\Models\StudentExtraInformation;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:students-list', ['only' => ['index']]);
        $this->middleware('permission:students-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:students-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:students-delete', ['only' => ['destroy']]);
        $this->middleware('permission:students-search', ['only' => ['search']]);
        $this->middleware('permission:students-show', ['only' => ['show']]);
        $this->middleware('permission:change-student-information', ['only' => ['changeInformation']]);
    }

    public function index()
    {
        $students = StudentInformation::where('guardian', session('id'))
            ->with('studentInfo')
            ->with('nationalityInfo')
            ->with('identificationTypeInfo')
            ->with('generalInformations')
            ->orderBy('student_id', 'asc')->paginate(15);
        if ($students->isEmpty()) {
            $students = [];
        }

        return view('Students.index', compact('students'));
    }

    public function create()
    {
        $birthplaces = Country::orderBy('en_short_name', 'asc')->get();
        $nationalities = Country::orderBy('nationality', 'asc')->select('nationality', 'id')->distinct()->get();
        $identificationTypes = CurrentIdentificationType::get();

        return view('Students.create', compact('birthplaces', 'identificationTypes', 'nationalities'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nationality' => 'required|exists:countries,id',
            'birthplace' => 'required|exists:countries,id',
            'current_identification_type' => 'required|exists:current_identification_types,id',
            'first_name_fa' => 'required|string',
            'last_name_fa' => 'required|string',
            'first_name_en' => 'required|string',
            'last_name_en' => 'required|string',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $nationality = $request->nationality;
        $current_identification_type = $request->current_identification_type;
        $birthplace = $request->birthplace;
        $birthdate = $request->birthdate;
        $current_identification_code = $request->current_identification_code;
        $gender = $request->gender;

        $me = User::find(session('id'));
        $user = new User();
        $user->password = Hash::make('Aa12345678');
        $user->status = 0;
        $user->save();
        $user->assignRole('Student');

        $generalInformation = new GeneralInformation();
        $generalInformation->user_id = $user->id;
        $generalInformation->first_name_fa = $request->first_name_fa;
        $generalInformation->last_name_fa = $request->last_name_fa;
        $generalInformation->first_name_en = $request->first_name_en;
        $generalInformation->last_name_en = $request->last_name_en;
        $generalInformation->birthdate = $birthdate;
        $generalInformation->birthplace = $birthplace;
        $generalInformation->nationality = $nationality;
        $generalInformation->gender = $gender;
        $generalInformation->save();

        $studentInformation = new StudentInformation();
        $studentInformation->student_id = $user->id;
        $studentInformation->guardian = $me->id;
        $studentInformation->current_nationality = $nationality;
        $studentInformation->current_identification_type = $current_identification_type;
        $studentInformation->current_identification_code = $current_identification_code;
        if ($me->hasRole('Parent(Father)')) {
            $studentInformation->parent_father_id = $me->id;
            $studentInformation->guardian_student_relationship = 1;
        } elseif ($me->hasRole('Parent(Mother)')) {
            $studentInformation->parent_mother_id = $me->id;
            $studentInformation->guardian_student_relationship = 2;
        } else {
            $studentInformation->guardian_student_relationship = 3;
        }
        $studentInformation->save();

        return redirect()->route('Students.index')
            ->with('success', 'Student added successfully');
    }

    public function show($id)
    {
        $studentInformations = StudentInformation::with('extraInformations')->where('student_id', $id)->where('guardian', session('id'))->first();
        if (! empty($studentInformations)) {
            $studentInformations = StudentInformation::with('generalInformations')->with('extraInformations')->where('student_id', $id)->where('guardian', session('id'))->first();
            return view('Students.show', compact('studentInformations'));
        }
        abort(403);
    }

    public function changeInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_identification_code' => 'required',
            'current_identification_type' => 'required|exists:current_identification_types,id',
            'current_nationality' => 'required|exists:countries,id',
            'father' => 'required|exists:users,id',
            'guardian' => 'required|exists:users,id',
            'guardian_student_relationship' => 'required|exists:guardian_student_relationships,id',
            'mother' => 'required|exists:users,id',
            'status' => 'required|exists:student_statuses,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator], 422);
        }
        $extraInformationTitles = $request->title;
        $extraInformationDescriptions = $request->description;
        if (count($extraInformationTitles) != count($extraInformationDescriptions)) {
            return response()->json(['error' => 'Extras count values is not same'], 422);
        }

        $studentInformation=StudentInformation::where('student_id',$request->user_id)->first();
        $studentInformation->parent_father_id = $request->father;
        $studentInformation->parent_mother_id = $request->mother;
        $studentInformation->guardian = $request->guardian;
        $studentInformation->guardian_student_relationship = $request->guardian_student_relationship;
        $studentInformation->current_nationality = $request->current_nationality;
        $studentInformation->current_identification_type = $request->current_identification_type;
        $studentInformation->current_identification_code = $request->current_identification_code;
        $studentInformation->status = $request->status;
        $studentInformation->save();

        StudentExtraInformation::where('student_informations_id', $studentInformation->id)->delete();

        foreach ($extraInformationTitles as $index => $titles) {
            $studentExtraInformation = new StudentExtraInformation();
            $studentExtraInformation->student_informations_id = $studentInformation->id;
            $studentExtraInformation->name = $titles;
            $studentExtraInformation->description = $extraInformationDescriptions[$index];
            $studentExtraInformation->save();
        }

        $this->logActivity('Student information saved successfully => '.$request->user_id, request()->ip(), request()->userAgent(), session('id'));

        return response()->json(['success' => 'Student information saved successfully!'], 200);
    }
}
