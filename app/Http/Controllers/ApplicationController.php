<?php

namespace App\Http\Controllers;

use App\Models\Branch\ApplicationReservation;
use App\Models\Branch\Applications;
use App\Models\Branch\ApplicationTiming;
use App\Models\Catalogs\AcademicYear;
use App\Models\Catalogs\Level;
use App\Models\Catalogs\PaymentMethod;
use App\Models\Document;
use App\Models\Finance\ApplicationReservationsInvoices;
use App\Models\StudentInformation;
use App\Models\User;
use App\Models\UserAccessInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:applications-list', ['only' => ['index']]);
        $this->middleware('permission:new-application-reserve', ['only' => ['create', 'store']]);
        $this->middleware('permission:show-application-reserve', ['only' => ['show']]);
        $this->middleware('permission:edit-application-reserve', ['only' => ['edit', 'update']]);
        $this->middleware('permission:remove-application', ['only' => ['destroy']]);
        $this->middleware('permission:remove-application-from-reserve', ['only' => ['removeFromReserve']]);
        $this->middleware('permission:change-status-of-application', ['only' => ['changeInterviewStatus']]);
    }

    public function index()
    {
        $me = User::find(session('id'));
        $applications = [];
        if ($me->hasRole('Parent(Father)') or $me->hasRole('Parent(Mother)')) {
            $myStudents = StudentInformation::where('guardian', $me->id)->pluck('student_id')->toArray();
            $applications = ApplicationReservation::with('applicationInfo')->with('studentInfo')->with('reservatoreInfo')->whereIn('student_id', $myStudents)->paginate(30);
        } elseif ($me->hasRole('Super Admin')) {
            $applications = ApplicationReservation::with('applicationInfo')->with('studentInfo')->with('reservatoreInfo')->paginate(30);
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            // Convert accesses to arrays and remove duplicates
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPA($myAllAccesses);

            // Finding academic years with status 1 in the specified schools
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->pluck('id')->toArray();

            // Finding application timings based on academic years
            $applicationTimings = ApplicationTiming::whereIn('academic_year', $academicYears)->pluck('id')->toArray();

            // Finding applications related to the application timings
            $applications = Applications::whereIn('application_timing_id', $applicationTimings)
                ->pluck('id')
                ->toArray();

            // Getting reservations of applications along with related information
            $applications = ApplicationReservation::with('applicationInfo')
                ->with('studentInfo')
                ->with('reservatoreInfo')
                ->whereIn('application_id', $applications)
                ->paginate(30);
        }

        if (empty($applications)) {
            $applications = [];
        }
        $this->logActivity(json_encode(['activity' => 'Getting Applications']), request()->ip(), request()->userAgent());

        return view('Applications.index', compact('applications', 'me'));

    }

    public function create()
    {
        $me = User::find(session('id'));
        if ($me->hasRole('Parent(Father)') or $me->hasRole('Parent(Mother)')) {
            $myStudents = StudentInformation::with('generalInformations')->where('guardian', $me->id)->orderBy('id')->get();
            $levels = Level::where('status', 1)->get();

            return view('Applications.create', compact('myStudents', 'levels'));
        }
    }

    public function show($id)
    {
        $me = User::find(session('id'));
        $applicationInfo = null;
        $applicationReservation = ApplicationReservation::find($id);
        if (empty($applicationReservation)) {
            $this->logActivity(json_encode(['activity' => 'Unauthorized Permission To Access Applications', 'entered_id' => $id]), request()->ip(), request()->userAgent());

            abort(403);
        }

        if ($me->hasRole('Parent(Father)') or $me->hasRole('Parent(Mother)')) {
            $myStudents = StudentInformation::where('guardian', $me->id)->pluck('student_id')->toArray();
            $applicationInfo = ApplicationReservation::with('levelInfo')->with('applicationInfo')->with('studentInfo')->with('reservatoreInfo')->with('applicationInvoiceInfo')->whereIn('student_id', $myStudents)->where('id', $id)->first();
        } elseif ($me->hasRole('Super Admin')) {
            $applicationInfo = ApplicationReservation::with('levelInfo')->with('applicationInfo')->with('studentInfo')->with('reservatoreInfo')->with('applicationInvoiceInfo')->where('id', $id)->first();
        } elseif ($me->hasRole('Principal') or $me->hasRole('Admissions Officer')) {
            // Convert accesses to arrays and remove duplicates
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPA($myAllAccesses);

            // Finding academic years with status 1 in the specified schools
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->pluck('id')->toArray();

            // Finding application timings based on academic years
            $applicationTimings = ApplicationTiming::whereIn('academic_year', $academicYears)->pluck('id')->toArray();

            // Finding applications related to the application timings
            $applications = Applications::whereIn('application_timing_id', $applicationTimings)
                ->pluck('id')
                ->toArray();

            // Getting reservations of applications along with related information
            $applicationInfo = ApplicationReservation::with('applicationInfo')
                ->with('studentInfo')
                ->with('reservatoreInfo')
                ->whereIn('application_id', $applications)
                ->where('id', $id)->first();
            if (empty($applicationInfo)) {
                $this->logActivity(json_encode(['activity' => 'Application Not Found', 'entered_id' => $id]), request()->ip(), request()->userAgent());

                abort(403);
            }
        }
        $this->logActivity(json_encode(['activity' => 'Getting Application Informations', 'entered_id' => $id]), request()->ip(), request()->userAgent());

        return view('Applications.show', compact('applicationInfo'));
    }

    public function destroy($id)
    {
        $me = User::find(session('id'));
        if (! $me->hasRole('Super Admin')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPA($myAllAccesses);
            $checkAccessToApplication = ApplicationTiming::with('academicYearInfo')
                ->with('applications')
                ->join('academic_years', 'application_timings.academic_year', '=', 'academic_years.id')
                ->join('applications', 'application_timings.id', '=', 'applications.application_timing_id')
                ->whereIn('academic_years.school_id', $filteredArray)
                ->where('applications.id', $id)
                ->select('application_timings.*', 'academic_years.id as academic_year_id')
                ->first();
            if (! $checkAccessToApplication) {
                $this->logActivity(json_encode(['activity' => 'Failed To Delete Application', 'entered_id' => $id]), request()->ip(), request()->userAgent());

                return redirect()->back()
                    ->withErrors(['errors' => 'Delete Failed!']);
            }
        }

        $removeApplication = Applications::find($id)->delete();

        if (! $removeApplication) {
            $this->logActivity(json_encode(['activity' => 'Failed To Delete Application', 'entered_id' => $id]), request()->ip(), request()->userAgent());

            return redirect()->back()
                ->withErrors(['errors' => 'Delete Failed!']);
        }
        $this->logActivity(json_encode(['activity' => 'Application Deleted', 'entered_id' => $id]), request()->ip(), request()->userAgent());

        return redirect()->back()
            ->with('success', 'Application deleted!');
    }

    public function removeFromReserve($id)
    {
        $me = User::find(session('id'));
        if (! $me->hasRole('Super Admin')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPA($myAllAccesses);
            $checkAccessToApplication = ApplicationTiming::with('academicYearInfo')
                ->with('applications')
                ->join('academic_years', 'application_timings.academic_year', '=', 'academic_years.id')
                ->join('applications', 'application_timings.id', '=', 'applications.application_timing_id')
                ->whereIn('academic_years.school_id', $filteredArray)
                ->where('applications.id', $id)
                ->select('application_timings.*', 'academic_years.id as academic_year_id')
                ->first();
            if (! $checkAccessToApplication) {
                $this->logActivity(json_encode(['activity' => 'Failed To Remove Application From Reservation', 'entered_id' => $id]), request()->ip(), request()->userAgent());

                return redirect()->back()
                    ->withErrors(['errors' => 'Delete Failed!']);
            }
        }

        $removeApplicationReserve = Applications::find($id);
        $removeApplicationReserve->reserved = 0;

        $applicationReservations = ApplicationReservation::where('application_id', $removeApplicationReserve->id)->first();
        $applicationReservationInvoice = ApplicationReservationsInvoices::where('a_reservation_id', $applicationReservations->id)->delete();
        $applicationReservations->delete();
        if (! $removeApplicationReserve->save() or ! $applicationReservations or ! $applicationReservationInvoice) {
            $this->logActivity(json_encode(['activity' => 'Failed To Remove Application From Reservation', 'entered_id' => $id]), request()->ip(), request()->userAgent());

            return redirect()->back()
                ->withErrors(['errors' => 'Remove Application Reservation Failed!']);
        }
        $this->logActivity(json_encode(['activity' => 'Application Reservation Changed', 'entered_id' => $id]), request()->ip(), request()->userAgent());

        return redirect()->back()
            ->with('success', 'Application Reservation Changed!');
    }

    public function changeApplicationStatus($id)
    {
        $me = User::find(session('id'));
        if (! $me->hasRole('Super Admin')) {
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPA($myAllAccesses);
            $checkAccessToApplication = ApplicationTiming::with('academicYearInfo')
                ->with('applications')
                ->join('academic_years', 'application_timings.academic_year', '=', 'academic_years.id')
                ->join('applications', 'application_timings.id', '=', 'applications.application_timing_id')
                ->whereIn('academic_years.school_id', $filteredArray)
                ->where('applications.id', $id)
                ->select('application_timings.*', 'academic_years.id as academic_year_id')
                ->first();
            if (! $checkAccessToApplication) {
                $this->logActivity(json_encode(['activity' => 'Failed To Change Application Status', 'entered_id' => $id]), request()->ip(), request()->userAgent());

                return redirect()->back()
                    ->withErrors(['errors' => 'Delete Failed!']);
            }
        }

        $changeApplicationStatus = Applications::find($id);
        if ($changeApplicationStatus->status == 0) {
            $changeApplicationStatus->status = 1;
        } else {
            $changeApplicationStatus->status = 0;
        }

        if (! $changeApplicationStatus->save()) {
            $this->logActivity(json_encode(['activity' => 'Failed To Change Application Status', 'entered_id' => $id]), request()->ip(), request()->userAgent());

            return redirect()->back()
                ->withErrors(['errors' => 'Change Interview Status Failed!']);
        }
        $this->logActivity(json_encode(['activity' => 'Interview Status Changed Successfully', 'entered_id' => $id]), request()->ip(), request()->userAgent());

        return redirect()->back()
            ->with('success', 'Interview Status Changed!');
    }

    public function getAcademicYearsByLevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|exists:levels,id',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Getting Academic Years By Level Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

            return response()->json(['error' => 'Error on choosing level!'], 422);
        }
        $level = $request->level;
        $academicYears = AcademicYear::where('status', 1)->whereJsonContains('levels', $level)->select('id', 'name')->get()->toArray();
        $this->logActivity(json_encode(['activity' => 'Getting Academic Years By Level', 'entered_level' => $level]), request()->ip(), request()->userAgent());

        return $academicYears;
    }

    public function getApplicationsByAcademicYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|exists:academic_years,id',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Getting Applications By Academic Year Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

            return response()->json(['error' => 'Error on choosing academic year!'], 422);
        }

        $applicationTimings = ApplicationTiming::with('applications')
            ->join('applications', 'application_timings.id', '=', 'applications.application_timing_id')
            ->where('application_timings.academic_year', $request->academic_year)
            ->where('applications.status', 1)
            ->where('applications.reserved', 0)
            ->select('applications.*', 'application_timings.id as application_timings_id')
            ->orderBy('application_timings.start_date')
            ->get();
        $this->logActivity(json_encode(['activity' => 'Getting Applications By Academic Year', 'entered_academic_year' => $request->academic_year]), request()->ip(), request()->userAgent());

        return $applicationTimings;
    }

    public function checkDateAndTimeToBeFreeApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application' => 'required|exists:applications,id',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Getting Date And Time To Be Free Application Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

            return response()->json(['error' => 'Error on choosing application!'], 422);
        }

        $application = $request->application;
        $applicationCheck = Applications::where('status', 1)->where('reserved', 0)->find($application);
        if (empty($applicationCheck)) {
            $this->logActivity(json_encode(['activity' => 'Application Reserved A Few Moments Ago', 'application_id' => $application]), request()->ip(), request()->userAgent());

            return response()->json(['error' => 'Unfortunately, the selected application was reserved a few minutes ago. Please choose another application'], 422);
        }
        $this->logActivity(json_encode(['activity' => 'Getting Date And Time To Be Free Application', 'application_id' => $application]), request()->ip(), request()->userAgent());

        return 0;
    }

    public function preparationForApplicationPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_and_time' => 'required|exists:applications,id',
            'academic_year' => 'required|exists:academic_years,id',
            'level' => 'required|exists:levels,id',
            'student' => 'required|exists:student_informations,id',
            'interview_type' => 'required',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Preparation For Application Payment Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $me = User::find(session('id'));
        $student = $request->student;
        $level = $request->level;
        $academic_year = $request->academic_year;
        $dateAndTime = $request->date_and_time;
        $interviewType = $request->interview_type;

        $studentInfo = StudentInformation::where('guardian', $me->id)->where('id', $student)->first();

        if (empty($studentInfo)) {
            $this->logActivity(json_encode(['activity' => 'Preparation For Application Payment Failed', 'errors' => 'Access To Student Denied', 'parameters' => json_encode($request->all())]), request()->ip(), request()->userAgent());

            abort(403);
        }

        $academicYearInfo = AcademicYear::whereJsonContains('levels', $level)->find($academic_year);
        if (empty($academicYearInfo)) {
            $this->logActivity(json_encode(['activity' => 'Preparation For Application Payment Failed', 'errors' => 'Access To Academic Year Info Denied', 'parameters' => json_encode($request->all())]), request()->ip(), request()->userAgent());

            abort(403);
        }

        $applicationCheck = Applications::where('status', 1)->where('reserved', 0)->find($dateAndTime);
        if (empty($applicationCheck)) {
            $this->logActivity(json_encode(['activity' => 'Application Reserved A Few Moments Ago', 'parameters' => json_encode($request->all())]), request()->ip(), request()->userAgent());

            return redirect()->back()->withErrors('Unfortunately, the selected application was reserved a few minutes ago. Please choose another application')->withInput();
        }

        $applicationReservation = new ApplicationReservation();
        $applicationReservation->application_id = $dateAndTime;
        $applicationReservation->student_id = $studentInfo->student_id;
        $applicationReservation->reservatore = $me->id;
        $applicationReservation->level = $level;
        $applicationReservation->interview_type = $interviewType;

        if ($applicationReservation->save()) {
            $applications = Applications::find($dateAndTime);
            $applications->reserved = 1;
            $applications->save();
        }
        $this->logActivity(json_encode(['activity' => 'Application Payment Prepared To Pay Successfully', 'parameters' => json_encode($request->all())]), request()->ip(), request()->userAgent());

        return redirect()->route('PrepareToPayApplication', $applicationReservation->id);
    }

    public function prepareToPay($application_id)
    {
        $me = User::find(session('id'));
        $checkApplication = null;
        if ($me->hasRole('Parent(Father)') or $me->hasRole('Parent(Mother)')) {
            $checkApplication = ApplicationReservation::with('applicationInfo')->where('reservatore', $me->id)->find($application_id);
            if (empty($checkApplication)) {
                $this->logActivity(json_encode(['activity' => 'Prepare To Pay Application Failed', 'application_id' => $application_id, 'errors' => 'Access Denied']), request()->ip(), request()->userAgent());

                abort(403);
            }
        }
        $createdAt = $checkApplication->created_at;

        $deadline = Carbon::parse($createdAt)->addHour()->toDateTimeString();
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $this->logActivity(json_encode(['activity' => 'Application Prepared To Pay', 'application_id' => $application_id]), request()->ip(), request()->userAgent());

        return view('Applications.application_payment', compact('checkApplication', 'deadline', 'paymentMethods'));
    }

    public function payApplicationFee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|exists:payment_methods,id',
            'id' => 'required|exists:application_reservations,id',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $applicationInformation = ApplicationReservation::find($request->id);

        switch ($request->payment_method) {
            case 1:
                $validator = Validator::make($request->all(), [
                    'document_file' => 'required|file|mimes:jpg,bmp,pdf,jpeg,png',
                ]);
                if ($validator->fails()) {
                    $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $path = $request->file('document_file')->store('public/uploads/Documents/'.session('id'));

                $document = new Document();
                $document->user_id = $applicationInformation->student_id;
                $document->document_type_id = 243;
                $document->src = $path;
                $document->save();

                if ($document) {
                    $applicationReservationInvoice = new ApplicationReservationsInvoices();
                    $applicationReservationInvoice->a_reservation_id = $request->id;
                    $applicationReservationInvoice->payment_information = json_encode([
                        'payment_method' => $request->payment_method,
                        'document_id' => $document->id,
                    ], true);
                    $applicationReservationInvoice->description = $request->description;
                    $applicationReservationInvoice->save();

                    if ($applicationReservationInvoice) {
                        $applicationInformation->payment_status = 2; //For Pending
                        $applicationInformation->save();
                        $this->logActivity(json_encode(['activity' => 'Application Reserved Successfully', 'reservation_invoice_id' => $applicationInformation->id]), request()->ip(), request()->userAgent());

                        return redirect()->route('Applications.index')->with('success', 'Application reserved successfully!');
                    }
                }
                break;
            default:
                $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent());

                abort(403);
        }
    }
}
