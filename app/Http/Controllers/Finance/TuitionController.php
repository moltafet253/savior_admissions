<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Branch\ApplicationReservation;
use App\Models\Branch\StudentApplianceStatus;
use App\Models\Catalogs\AcademicYear;
use App\Models\Catalogs\PaymentMethod;
use App\Models\Document;
use App\Models\Finance\Discount;
use App\Models\Finance\DiscountDetail;
use App\Models\Finance\Tuition;
use App\Models\Finance\TuitionDetail;
use App\Models\Finance\TuitionInvoiceDetails;
use App\Models\Finance\TuitionInvoices;
use App\Models\StudentInformation;
use App\Models\User;
use App\Models\UserAccessInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class TuitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tuition-list', ['only' => ['index']]);
        $this->middleware('permission:tuition-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tuition-show', ['only' => ['show']]);
        $this->middleware('permission:tuition-change-price', ['only' => ['changeTuitionPrice']]);
    }

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $me = User::find(auth()->user()->id);
        $tuitions = [];
        if ($me->hasRole('Super Admin')) {
            $tuitions = Tuition::with('academicYearInfo')->orderBy('academic_year', 'desc')->paginate(10);
        } elseif ($me->hasRole('Principal') or $me->hasRole('Financial Manager')) {
            // Convert accesses to arrays and remove duplicates
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPF($myAllAccesses);

            // Finding academic years with status 1 in the specified schools
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->pluck('id')->toArray();

            $tuitions = Tuition::with('academicYearInfo')->whereIn('academic_year', $academicYears)->orderBy('academic_year', 'desc')->paginate(10);
        }

        if ($tuitions->isEmpty()) {
            $tuitions = [];
        }
        $this->logActivity(json_encode(['activity' => 'Getting Tuitions']), request()->ip(), request()->userAgent());

        return view('Finance.Tuition.index', compact('tuitions'));
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $me = User::find(auth()->user()->id);
        $tuitions = [];
        if ($me->hasRole('Super Admin')) {
            $tuitions = Tuition::with('academicYearInfo')->with('allTuitions')->orderBy('academic_year', 'desc')->find($id);
        } elseif ($me->hasRole('Principal') or $me->hasRole('Financial Manager')) {
            // Convert accesses to arrays and remove duplicates
            $myAllAccesses = UserAccessInformation::where('user_id', $me->id)->first();
            $filteredArray = $this->getFilteredAccessesPF($myAllAccesses);

            // Finding academic years with status 1 in the specified schools
            $academicYears = AcademicYear::where('status', 1)->whereIn('school_id', $filteredArray)->pluck('id')->toArray();

            $tuitions = Tuition::with('academicYearInfo')->with('allTuitions')->whereIn('academic_year', $academicYears)->orderBy('academic_year', 'desc')->find($id);
        }

        if (empty($tuitions)) {
            $tuitions = [];
        }
        $this->logActivity(json_encode(['activity' => 'Getting Academic Year Tuitions Information For Edit', 'tuition_id' => $id]), request()->ip(), request()->userAgent());

        return view('Finance.Tuition.edit', compact('tuitions'));

    }

    public function changeTuitionPrice(Request $request)
    {
        $requestData = $request->all();

        foreach ($requestData as $key => $value) {
            $requestData[$key] = preg_replace('/[^0-9]/', '', $value);
        }

        $validator = Validator::make($requestData, [
            'tuition_details_id' => 'required|integer|exists:tuition_details,id',
            'full_payment_irr' => 'required|integer',
            'full_payment_usd' => 'required|integer',
            'two_installment_amount_irr' => 'required|integer',
            'two_installment_amount_usd' => 'required|integer',
            'two_installment_advance_irr' => 'required|integer',
            'two_installment_advance_usd' => 'required|integer',
            'two_installment_each_installment_irr' => 'required|integer',
            'two_installment_each_installment_usd' => 'required|integer',
            'date_of_installment1_two' => 'required|date',
            'date_of_installment2_two' => 'required|date',
            'four_installment_amount_irr' => 'required|integer',
            'four_installment_amount_usd' => 'required|integer',
            'four_installment_advance_irr' => 'required|integer',
            'four_installment_advance_usd' => 'required|integer',
            'four_installment_each_installment_irr' => 'required|integer',
            'four_installment_each_installment_usd' => 'required|integer',
            'date_of_installment1_four' => 'required|date',
            'date_of_installment2_four' => 'required|date',
            'date_of_installment3_four' => 'required|date',
            'date_of_installment4_four' => 'required|date',
        ]);

        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Change Tuition Price Failed', 'values' => $request, 'errors' => json_encode($validator->errors())]), request()->ip(), request()->userAgent());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'errors' => $validator->errors(),
            ], 419);
        }

        $tuition = TuitionDetail::find($request->tuition_details_id);
        $tuition->full_payment = json_encode(['full_payment_irr' => $request->full_payment_irr, 'full_payment_usd' => $request->full_payment_usd], true);
        $tuition->two_installment_payment = json_encode([
            'two_installment_amount_irr' => $request->two_installment_amount_irr,
            'two_installment_amount_usd' => $request->two_installment_amount_usd,
            'two_installment_advance_irr' => $request->two_installment_advance_irr,
            'two_installment_advance_usd' => $request->two_installment_advance_usd,
            'two_installment_each_installment_irr' => $request->two_installment_each_installment_irr,
            'two_installment_each_installment_usd' => $request->two_installment_each_installment_usd,
            'date_of_installment1_two' => $request->date_of_installment1_two,
            'date_of_installment2_two' => $request->date_of_installment2_two,
        ], true);
        $tuition->four_installment_payment = json_encode([
            'four_installment_amount_irr' => $request->four_installment_amount_irr,
            'four_installment_amount_usd' => $request->four_installment_amount_usd,
            'four_installment_advance_irr' => $request->four_installment_advance_irr,
            'four_installment_advance_usd' => $request->four_installment_advance_usd,
            'four_installment_each_installment_irr' => $request->four_installment_each_installment_irr,
            'four_installment_each_installment_usd' => $request->four_installment_each_installment_usd,
            'date_of_installment1_four' => $request->date_of_installment1_four,
            'date_of_installment2_four' => $request->date_of_installment2_four,
            'date_of_installment3_four' => $request->date_of_installment3_four,
            'date_of_installment4_four' => $request->date_of_installment4_four,
        ], true);
        $tuition->save();
        $this->logActivity(json_encode(['activity' => 'Tuition Fee Changed', 'tuition_id' => $request->tuition_id, 'price' => $request->price]), request()->ip(), request()->userAgent());

        return response()->json(['message' => 'Tuition fee changed successfully!'], 200);
    }

    public function payTuition($student_id)
    {
        if (empty($this->getActiveAcademicYears())) {
            abort(403);
        }

        $studentApplianceStatus = StudentApplianceStatus::with('studentInfo')->with('academicYearInfo')->where('student_id', $student_id)->where('tuition_payment_status', 'Pending')->whereIn('academic_year', $this->getActiveAcademicYears())->first();

        if (empty($studentApplianceStatus)) {
            abort(403);
        }

        $applicationInfo = ApplicationReservation::join('applications', 'application_reservations.application_id', '=', 'applications.id')
            ->join('application_timings', 'applications.application_timing_id', '=', 'application_timings.id')
            ->join('interviews', 'applications.id', '=', 'interviews.application_id')
            ->where('application_reservations.student_id', $student_id)
            ->where('applications.reserved', 1)
            ->where('application_reservations.payment_status', 1)
            ->where('applications.interviewed', 1)
            ->where('interviews.interview_type', 3)
            ->whereIn('application_timings.academic_year', $this->getActiveAcademicYears())
            ->orderByDesc('application_reservations.id')
            ->first();

        //Get tuition price
        $tuition = Tuition::join('tuition_details', 'tuitions.id', '=', 'tuition_details.tuition_id')
            ->where('tuitions.academic_year', $applicationInfo->academic_year)
            ->where('tuition_details.level', $applicationInfo->level)
            ->first();

        $paymentMethods = PaymentMethod::get();

        //Discount Percentages
        $discountPercentages=0;
        if (isset(json_decode($applicationInfo->interview_form, true)['discount'])) {
            $interviewFormDiscounts = json_decode($applicationInfo->interview_form, true)['discount'];
            $discountPercentages = DiscountDetail::whereIn('id', $interviewFormDiscounts)->pluck('percentage')->sum();
        }

        //Get all students with paid status in all active academic years
        $me = auth()->user()->id;

        $allStudentsWithMyGuardian = StudentInformation::where('guardian', $me)->pluck('student_id')->toArray();
        $allStudentsWithPaidStatusInActiveAcademicYear = StudentApplianceStatus::with('studentInfo')
            ->with('academicYearInfo')
            ->whereIn('student_id', $allStudentsWithMyGuardian)
            ->where('tuition_payment_status', 'Paid')
            ->whereIn('academic_year', $this->getActiveAcademicYears())
            ->count();

        $familyDiscount = 0;
        if ($allStudentsWithPaidStatusInActiveAcademicYear == 2) {
            $familyDiscount = 25;
        }
        if ($allStudentsWithPaidStatusInActiveAcademicYear == 3) {
            $familyDiscount = 30;
        }
        if ($allStudentsWithPaidStatusInActiveAcademicYear > 4) {
            $familyDiscount = 40;
        }

        return view('Finance.Tuition.Pay.index', compact('studentApplianceStatus', 'tuition', 'applicationInfo', 'paymentMethods', 'discountPercentages', 'familyDiscount'));
    }

    public function tuitionPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:1,2,3',
            'payment_method' => 'required|exists:payment_methods,id',
            'student_id' => 'required|exists:student_appliance_statuses,student_id',
            'appliance_id' => 'required|exists:student_appliance_statuses,id',
            'accept' => 'required|accepted',
            'description' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator->errors())]), request()->ip(), request()->userAgent());

            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (empty($this->getActiveAcademicYears())) {
            abort(403);
        }
        $appliance_id = $request->appliance_id;
        $student_id = $request->student_id;
        $description = $request->description;
        $studentApplianceStatus = StudentApplianceStatus::with('studentInfo')
            ->with('academicYearInfo')
            ->where('id', $appliance_id)
            ->where('tuition_payment_status', 'Pending')
            ->whereIn('academic_year', $this->getActiveAcademicYears())
            ->first();

        if (empty($studentApplianceStatus)) {
            abort(403);
        }

        $applicationInfo = ApplicationReservation::join('applications', 'application_reservations.application_id', '=', 'applications.id')
            ->join('application_timings', 'applications.application_timing_id', '=', 'application_timings.id')
            ->join('interviews', 'applications.id', '=', 'interviews.application_id')
            ->where('application_reservations.student_id', $student_id)
            ->where('applications.reserved', 1)
            ->where('application_reservations.payment_status', 1)
            ->where('applications.interviewed', 1)
            ->where('interviews.interview_type', 3)
            ->whereIn('application_timings.academic_year', $this->getActiveAcademicYears())
            ->orderByDesc('application_reservations.id')
            ->first();

        if (empty($applicationInfo)) {
            abort(403);
        }

        //Get tuition price
        $tuition = Tuition::join('tuition_details', 'tuitions.id', '=', 'tuition_details.tuition_id')
            ->where('tuitions.academic_year', $applicationInfo->academic_year)
            ->where('tuition_details.level', $applicationInfo->level)
            ->first();

        $paymentMethod = $request->payment_method;
        $paymentType = $request->payment_type;

        //Discount Percentages
        $discountPercentages=0;
        if (isset(json_decode($applicationInfo->interview_form, true)['discount'])) {
            $interviewFormDiscounts = json_decode($applicationInfo->interview_form, true)['discount'];
            $discountPercentages = DiscountDetail::whereIn('id', $interviewFormDiscounts)->pluck('percentage')->sum();
        }

        //Get all students with paid status in all active academic years
        $allStudentsWithPaidStatusInActiveAcademicYear = StudentApplianceStatus::with('studentInfo')
            ->with('academicYearInfo')
            ->where('student_id', '!=', $student_id)
            ->where('tuition_payment_status', 'Paid')
            ->whereIn('academic_year', $this->getActiveAcademicYears())
            ->count();

        $familyDiscount = 0;
        if ($allStudentsWithPaidStatusInActiveAcademicYear == 2) {
            $familyDiscount = 25;
        }
        if ($allStudentsWithPaidStatusInActiveAcademicYear == 3) {
            $familyDiscount = 30;
        }
        if ($allStudentsWithPaidStatusInActiveAcademicYear > 4) {
            $familyDiscount = 40;
        }

        //Amount information
        $fullPayment = json_decode($tuition->full_payment, true);
        $fullPaymentAmount = str_replace(',', '', $fullPayment['full_payment_irr']);
        $fullPaymentAmountWithDiscounts = $fullPaymentAmount - ((($fullPaymentAmount * $familyDiscount) / 100) + (($fullPaymentAmount * $discountPercentages) / 100));

        $twoInstallmentPayment = json_decode($tuition->two_installment_payment, true);
        $twoInstallmentPaymentAmount = str_replace(',', '', $twoInstallmentPayment['two_installment_amount_irr']);
        $twoInstallmentPaymentAmountWithDiscounts = $twoInstallmentPaymentAmount - ((($twoInstallmentPaymentAmount * $familyDiscount) / 100) + (($twoInstallmentPaymentAmount * $discountPercentages) / 100));
        $twoInstallmentAdvance=str_replace(',', '', $twoInstallmentPayment['two_installment_advance_irr']);

        $fourInstallmentPayment = json_decode($tuition->four_installment_payment, true);
        $fourInstallmentPaymentAmount = str_replace(',', '', $fourInstallmentPayment['four_installment_amount_irr']);
        $fourInstallmentPaymentAmountWithDiscounts = $fourInstallmentPaymentAmount - ((($fourInstallmentPaymentAmount * $familyDiscount) / 100) + (($fourInstallmentPaymentAmount * $discountPercentages) / 100));
        $fourInstallmentAdvance=str_replace(',', '', $fourInstallmentPayment['four_installment_advance_irr']);

        /*
         * Payment Types:
        1 for full payment
        2 for 2 installments
        3 for 3 installments
         */

        //Save files if payment method is offline
        if ($paymentMethod == 1) {
            switch ($paymentType) {
                case 1:
                    $validator = Validator::make($request->all(), [
                        'document_file_full_payment1' => 'required|mimes:png,jpg,jpeg,pdf,bmp',
                        'document_file_full_payment2' => 'nullable|mimes:png,jpg,jpeg,pdf,bmp',
                        'document_file_full_payment3' => 'nullable|mimes:png,jpg,jpeg,pdf,bmp',
                    ]);
                    if ($validator->fails()) {
                        $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator->errors())]), request()->ip(), request()->userAgent());

                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    if ($request->file('document_file_full_payment1') !== null) {
                        $extension = $request->file('document_file_full_payment1')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileFullPayment1Src = $request->file('document_file_full_payment1')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }
                    if ($request->file('document_file_full_payment2') !== null) {
                        $extension = $request->file('document_file_full_payment2')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileFullPayment2Src = $request->file('document_file_full_payment1')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }
                    if ($request->file('document_file_full_payment3') !== null) {
                        $extension = $request->file('document_file_full_payment3')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileFullPayment3Src = $request->file('document_file_full_payment1')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }
                    $filesSrc = [];

                    if (isset($documentFileFullPayment1Src) and $documentFileFullPayment1Src !== null) {
                        $filesSrc['file1'] = $documentFileFullPayment1Src;
                    }

                    if (isset($documentFileFullPayment2Src) and $documentFileFullPayment2Src !== null) {
                        $filesSrc['file2'] = $documentFileFullPayment2Src;
                    }

                    if (isset($documentFileFullPayment3Src) and $documentFileFullPayment3Src !== null) {
                        $filesSrc['file3'] = $documentFileFullPayment3Src;
                    }

                    break;
                case 2:
                case 3:
                    $validator = Validator::make($request->all(), [
                        'document_file_offline_installment1' => 'required|mimes:png,jpg,jpeg,pdf,bmp',
                        'document_file_offline_installment2' => 'nullable|mimes:png,jpg,jpeg,pdf,bmp',
                        'document_file_offline_installment3' => 'nullable|mimes:png,jpg,jpeg,pdf,bmp',
                    ]);
                    if ($validator->fails()) {
                        $this->logActivity(json_encode(['activity' => 'Application Payment Failed', 'errors' => json_encode($validator->errors())]), request()->ip(), request()->userAgent());

                        return redirect()->back()->withErrors($validator)->withInput();
                    }
                    if ($request->file('document_file_offline_installment1') !== null) {
                        $extension = $request->file('document_file_offline_installment1')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileOfflineInstallment1Src = $request->file('document_file_offline_installment1')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }
                    if ($request->file('document_file_offline_installment2') !== null) {
                        $extension = $request->file('document_file_offline_installment2')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileOfflineInstallment2Src = $request->file('document_file_offline_installment2')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }
                    if ($request->file('document_file_offline_installment3') !== null) {
                        $extension = $request->file('document_file_offline_installment3')->getClientOriginalExtension();

                        $bankSlip1 = 'Tuition_'.now()->format('Y-m-d_H-i-s').'.'.$extension;
                        $documentFileOfflineInstallment3Src = $request->file('document_file_offline_installment3')->storeAs(
                            'public/uploads/Documents/'.$student_id.'/Appliance_'."$appliance_id".'/Tuitions',
                            $bankSlip1
                        );
                    }

                    $filesSrc = [];

                    if (isset($documentFileOfflineInstallment1Src) and $documentFileOfflineInstallment1Src !== null) {
                        $filesSrc['file1'] = $documentFileOfflineInstallment1Src;
                    }

                    if (isset($documentFileOfflineInstallment2Src) and $documentFileOfflineInstallment2Src !== null) {
                        $filesSrc['file2'] = $documentFileOfflineInstallment2Src;
                    }

                    if (isset($documentFileOfflineInstallment3Src) and $documentFileOfflineInstallment3Src !== null) {
                        $filesSrc['file3'] = $documentFileOfflineInstallment3Src;
                    }

                    break;
            }

            foreach ($filesSrc as $file){
                $document=Document::create([
                    'user_id'=>auth()->user()->id,
                    'document_type_id'=>7,
                    'src'=>$file,
                    'description'=>$description,
                ]);
            }
        }

        //Make new tuition invoice
        $tuitionInvoice = TuitionInvoices::firstOrCreate([
            'appliance_id' => $appliance_id,
            'payment_type' => $paymentType,
        ]);

        //Make invoice details by payment type
        switch ($paymentType){
            case 1:
                switch ($paymentMethod){
                    case 1:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$fullPaymentAmountWithDiscounts;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=2;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'files'=>$filesSrc,'tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $studentApplianceStatus->tuition_payment_status='Pending For Review';
                        $studentApplianceStatus->save();
                        return redirect()->route('TuitionInvoices.index')->with(['success' => "You have successfully paid tuition amount. Please wait for financial approval!"]);
                        break;
                    case 2:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$fullPaymentAmountWithDiscounts;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=0;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $invoice = (new Invoice)->amount($fullPaymentAmountWithDiscounts);

                        return Payment::via('behpardakht')->callbackUrl(env('APP_URL').'/VerifyTuitionPayment')->purchase(
                            $invoice,
                            function ($driver, $transactionID) use ($fullPaymentAmountWithDiscounts, $tuitionInvoiceDetails) {
                                $dataInvoice = new \App\Models\Invoice();
                                $dataInvoice->user_id = auth()->user()->id;
                                $dataInvoice->type = 'Tuition Payment (Full Payment)';
                                $dataInvoice->amount = $fullPaymentAmountWithDiscounts;
                                $dataInvoice->description = json_encode(['amount' => $fullPaymentAmountWithDiscounts, 'invoice_details_id' => $tuitionInvoiceDetails->id], true);
                                $dataInvoice->transaction_id = $transactionID;
                                $dataInvoice->save();
                            }
                        )->pay()->render();
                        break;
                }
                break;
            case 2:
                switch ($paymentMethod){
                    case 1:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$twoInstallmentAdvance;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=2;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'files'=>$filesSrc,'tuition_type'=>'Advance','tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $studentApplianceStatus->tuition_payment_status='Pending For Review';
                        $studentApplianceStatus->save();
                        return redirect()->route('TuitionInvoices.index')->with(['success' => "You have successfully paid tuition amount. Please wait for financial approval!"]);
                        break;
                    case 2:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$twoInstallmentAdvance;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=0;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'tuition_type'=>'Advance','tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $invoice = (new Invoice)->amount($twoInstallmentAdvance);

                        return Payment::via('behpardakht')->callbackUrl(env('APP_URL').'/VerifyTuitionPayment')->purchase(
                            $invoice,
                            function ($driver, $transactionID) use ($twoInstallmentAdvance, $tuitionInvoiceDetails) {
                                $dataInvoice = new \App\Models\Invoice();
                                $dataInvoice->user_id = auth()->user()->id;
                                $dataInvoice->type = 'Tuition Payment (Two Installment Advance)';
                                $dataInvoice->amount = $twoInstallmentAdvance;
                                $dataInvoice->description = json_encode(['amount' => $twoInstallmentAdvance, 'invoice_details_id' => $tuitionInvoiceDetails->id], true);
                                $dataInvoice->transaction_id = $transactionID;
                                $dataInvoice->save();
                            }
                        )->pay()->render();
                        break;
                }
                break;
            case 3:
                switch ($paymentMethod){
                    case 1:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$fourInstallmentAdvance;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=2;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'files'=>$filesSrc,'tuition_type'=>'Advance','tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $studentApplianceStatus->tuition_payment_status='Pending For Review';
                        $studentApplianceStatus->save();
                        return redirect()->route('TuitionInvoices.index')->with(['success' => "You have successfully paid tuition amount. Please wait for financial approval!"]);
                        break;
                    case 2:
                        $tuitionInvoiceDetails=new TuitionInvoiceDetails();
                        $tuitionInvoiceDetails->tuition_invoice_id=$tuitionInvoice->id;
                        $tuitionInvoiceDetails->amount=$fourInstallmentAdvance;
                        $tuitionInvoiceDetails->payment_method=$paymentMethod;
                        $tuitionInvoiceDetails->is_paid=0;
                        $tuitionInvoiceDetails->description=json_encode(['user_description'=>$description,'tuition_type'=>'Advance','tuition_details_id'=>$tuition->id],true);
                        $tuitionInvoiceDetails->save();

                        $invoice = (new Invoice)->amount($fourInstallmentAdvance);

                        return Payment::via('behpardakht')->callbackUrl(env('APP_URL').'/VerifyTuitionPayment')->purchase(
                            $invoice,
                            function ($driver, $transactionID) use ($fourInstallmentAdvance, $tuitionInvoiceDetails) {
                                $dataInvoice = new \App\Models\Invoice();
                                $dataInvoice->user_id = auth()->user()->id;
                                $dataInvoice->type = 'Tuition Payment (Four Installment Advance)';
                                $dataInvoice->amount = $fourInstallmentAdvance;
                                $dataInvoice->description = json_encode(['amount' => $fourInstallmentAdvance, 'invoice_details_id' => $tuitionInvoiceDetails->id], true);
                                $dataInvoice->transaction_id = $transactionID;
                                $dataInvoice->save();
                            }
                        )->pay()->render();
                        break;
                }
                break;
        }

        dd($request->all());

        return view('Finance.Tuition.Pay.index', compact('studentApplianceStatus', 'tuition', 'applicationInfo', 'paymentMethod', 'discountPercentages', 'familyDiscount'));
    }
}
