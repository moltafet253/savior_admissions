<?php

namespace App\Http\Controllers\BranchInfo;

use App\Http\Controllers\Controller;
use App\Models\Branch\AcademicYearClass;
use App\Models\Catalogs\AcademicYear;
use App\Models\Catalogs\EducationType;
use App\Models\Catalogs\Level;
use App\Models\Gender;
use App\Models\User;
use App\Models\UserAccessInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearClassController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:academic-year-class-list', ['only' => ['index']]);
        $this->middleware('permission:academic-year-class-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:academic-year-class-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:academic-year-class-delete', ['only' => ['destroy']]);
        $this->middleware('permission:academic-year-class-search', ['only' => ['search']]);
    }

    public function index()
    {
        $me = User::find(session('id'));
        if ($me->hasRole('Super Admin')) {
            $academicYearClasses = AcademicYearClass::with('academicYearInfo')->with('levelInfo')->with('educationTypeInfo')->with('educationGenderInfo')->orderBy('id', 'desc')->paginate(10);
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            // Retrieve user access information
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();

            // Convert accesses to arrays and remove duplicates
            $principalAccess = explode("|", $myAllAccesses->principal);
            $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
            $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));

            // Retrieve academic years associated with the accesses
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->get();

            // Convert the 'id' column to an array
            $academicYearIds = $academicYears->pluck('id')->toArray();

            // Retrieve classes associated with academic years
            $academicYearClasses = AcademicYearClass::with('academicYearInfo')->with('levelInfo')->with('educationTypeInfo')->with('educationGenderInfo')->orderBy('id', 'desc')->whereIn('academic_year', $academicYearIds)->paginate(10);
        }
        return view('BranchInfo.AcademicYearClasses.index', compact('academicYearClasses'));
    }

    public function create()
    {
        $me = User::find(session('id'));
        if ($me->hasRole('Super Admin')) {
            $academicYears = AcademicYear::where('status', 1)->get();
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $principalAccess = explode("|", $myAllAccesses->principal);
            $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
            $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->get();
        }

        $levels = Level::where('status', 1)->get();
        $educationTypes = EducationType::where('status', 1)->get();
        $educationGenders = Gender::get();
        return view('BranchInfo.AcademicYearClasses.create', compact('academicYears', 'levels', 'educationTypes', 'educationGenders'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'academic_year' => 'required|exists:academic_years,id',
            'level' => 'required|exists:levels,id',
            'education_type' => 'required|exists:education_types,id',
            'capacity' => 'required|integer|max:60|min:1',
            'education_gender' => 'required|exists:genders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $class = new AcademicYearClass();
        $class->name = $request->name;
        $class->academic_year = $request->academic_year;
        $class->level = $request->level;
        $class->education_type = $request->education_type;
        $class->capacity = $request->capacity;
        $class->education_gender = $request->education_gender;
        $class->save();

        return redirect()->route('AcademicYearClasses.index')
            ->with('success', 'Academic year class added successfully');
    }

    public function edit($id)
    {

    }
}
