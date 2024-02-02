<?php

namespace App\Http\Controllers\BranchInfo;

use App\Http\Controllers\Controller;
use App\Models\Branch\AcademicYearClass;
use App\Models\Branch\ApplicationTiming;
use App\Models\Catalogs\AcademicYear;
use App\Models\Catalogs\EducationType;
use App\Models\Catalogs\Level;
use App\Models\Gender;
use App\Models\User;
use App\Models\UserAccessInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationTimingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:application-timing-list', ['only' => ['index']]);
        $this->middleware('permission:application-timing-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:application-timing-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:application-timing-delete', ['only' => ['destroy']]);
        $this->middleware('permission:application-timing-search', ['only' => ['searchApplicationTiming']]);
    }

    public function index()
    {
        $me = User::find(session('id'));
        $applicationTimings = [];
        if ($me->hasRole('Super Admin')) {
            $applicationTimings = ApplicationTiming::with('academicYearInfo')->orderBy('id', 'desc')->paginate(20);
            if ($applicationTimings->isEmpty()) {
                $applicationTimings = [];
            }
        } elseif (!$me->hasRole('Super Admin')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            if ($myAllAccesses != null) {
                $principalAccess = explode("|", $myAllAccesses->principal);
                $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
                $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
                $applicationTimings = ApplicationTiming::join('academic_years', 'application_timings.academic_year', '=', 'academic_years.id')
                    ->whereIn('academic_years.school_id', $filteredArray)
                    ->paginate(20);
                if ($applicationTimings->isEmpty()) {
                    $applicationTimings = [];
                }
            }
        }
        return view('BranchInfo.ApplicationTimings.index', compact('applicationTimings'));
    }

    public function create()
    {
        $me = User::find(session('id'));
        $academicYears = [];
        if ($me->hasRole('Super Admin')) {
            $academicYears = AcademicYear::where('status', 1)->get();
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            if (isset($myAllAccesses->principal) or isset($myAllAccesses->admissions_officer)) {
                $principalAccess = explode("|", $myAllAccesses->principal);
                $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
                $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
                $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->get();
                if ($academicYears->count() == 0) {
                    $academicYears = [];
                }
            } else {
                $academicYears = [];
            }
        }
        return view('BranchInfo.ApplicationTimings.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|exists:academic_years,id',
            'student_application_type' => 'required|string|in:All,Presently Studying',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interview_time' => 'required|integer|min:1',
            'delay_between_reserve' => 'required|integer|min:1',
            'interviewers' => 'required|exists:users,id',
            'interview_fee' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $me = User::find(session('id'));
        $academicYears = [];
        if ($me->hasRole('Super Admin')) {
            $academicYears = AcademicYear::where('status', 1)->get();
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            if (isset($myAllAccesses->principal) or isset($myAllAccesses->admissions_officer)) {
                $principalAccess = explode("|", $myAllAccesses->principal);
                $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
                $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
                $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->get();
                if ($academicYears->count() == 0) {
                    $academicYears = [];
                }
            } else {
                $academicYears = [];
            }
        }

        if (!empty($academicYears)){
            $applicationTiming=new ApplicationTiming();
            $applicationTiming->academic_year=$request->academic_year;
            $applicationTiming->students_application_type=$request->student_application_type;
            $applicationTiming->start_date=$request->start_date;
            $applicationTiming->start_time=$request->start_time;
            $applicationTiming->end_date=$request->end_date;
            $applicationTiming->end_time=$request->end_time;
            $applicationTiming->interview_time=$request->interview_time;
            $applicationTiming->delay_between_reserve=$request->delay_between_reserve;
            $applicationTiming->interviewers=json_encode($request->interviewers,true);
            $applicationTiming->fee=$request->interview_fee;
            $applicationTiming->save();
        }

        return redirect()->route('Applications.index')
            ->with('success', 'Application timing created successfully');
    }

    public function show($id)
    {
        $me = User::find(session('id'));
        $applicationTiming = [];
        if ($me->hasRole('Super Admin')) {
            $applicationTiming = ApplicationTiming::find($id);
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            if (isset($myAllAccesses->principal) or isset($myAllAccesses->admissions_officer)) {
                $principalAccess = explode("|", $myAllAccesses->principal);
                $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
                $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
                $applicationTiming = ApplicationTiming::
                    with('academicYearInfo')
                    ->join('academic_years', 'application_timings.academic_year', '=', 'academic_years.id')
                    ->whereIn('academic_years.school_id', $filteredArray)
                    ->where('application_timings.id',$id)
                    ->get();
            }
        }
        return view('BranchInfo.ApplicationTimings.show', compact('applicationTiming'));
    }
    public function interviewers(Request $request)
    {
        $me = User::find(session('id'));
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|exists:academic_years,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Error on choosing academic year!'], 422);
        }

        $academicYear = $request->academic_year;

        if ($me->hasRole('Super Admin')) {
            $academicYearInterviewers = AcademicYear::where('status', 1)->where('id', $academicYear)->pluck('employees')->first();
            $interviewers = User::whereIn('id', json_decode($academicYearInterviewers, true)['Interviewer'][0])->where('status', 1)->select('name', 'family', 'id')->get()->keyBy('id')->toArray();
        } else {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            if (isset($myAllAccesses->principal) or isset($myAllAccesses->admissions_officer)) {
                $principalAccess = explode("|", $myAllAccesses->principal);
                $admissionsOfficerAccess = explode("|", $myAllAccesses->admissions_officer);
                $filteredArray = array_filter(array_unique(array_merge($principalAccess, $admissionsOfficerAccess)));
                $academicYearInterviewers = AcademicYear::where('status', 1)->where('id', $academicYear)->whereIn('school_id', $filteredArray)->pluck('employees')->first();
                if (empty($academicYearInterviewers)) {
                    $academicYearInterviewers = [];
                }
            } else {
                $academicYearInterviewers = [];
            }
            $interviewers = User::whereIn('id', json_decode($academicYearInterviewers, true)['Interviewer'][0])->where('status', 1)->select('name', 'family', 'id')->get()->toArray();
            if (empty($interviewers)) {
                $interviewers = [];
            }
        }
        return $interviewers;
    }
}
