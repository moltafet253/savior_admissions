<?php

namespace App\Http\Controllers;

use App\Models\Branch\ApplicationReservation;
use App\Models\Branch\Applications;
use App\Models\Branch\StudentApplianceStatus;
use App\Models\Finance\ApplicationReservationsInvoices;
use App\Models\Finance\TuitionDetail;
use App\Models\Finance\TuitionInvoiceDetails;
use App\Models\Finance\TuitionInvoices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    public function verifyApplicationPayment(Request $request)
    {
        $transaction_id = \App\Models\Invoice::where('type', 'Application Reservation')->where('transaction_id', $request->RefId)->latest()->first();
        $user = User::find($transaction_id->user_id);

        if (Auth::loginUsingId($user->id)) {
            Session::put('id', $user->id);
        } else {
            abort(419);
        }

        switch ($request->ResCode) {
            case 0:
                $receipt = Payment::transactionId($request->RefId)->verify();

                if ($receipt) {
                    $transactionDetail = \App\Models\Invoice::where('type', 'Application Reservation')->where('transaction_id', $request->RefId)->where('amount', $request->FinalAmount)->latest()->first();

                    if (empty($transactionDetail)) {
                        abort(419);
                    }

                    $invoiceDescription = json_decode($transactionDetail->description, true);

                    $applicationReservation = ApplicationReservation::where('payment_status', 0)->where('id', $invoiceDescription['reservation_id'])->first();
                    $applicationReservation->payment_status = 1;
                    $applicationReservation->save();

                    $applicationReservationInvoice = new ApplicationReservationsInvoices();
                    $applicationReservationInvoice->a_reservation_id = $applicationReservation->id;
                    $applicationReservationInvoice->payment_information = json_encode(['payment_method' => 2, json_encode($request->all(), true)], true);
                    $applicationReservationInvoice->save();

                    $application = Applications::find($applicationReservation->application_id);
                    $application->reserved = 1;
                    $application->save();

                    $applianceStatus = new StudentApplianceStatus();
                    $applianceStatus->student_id = $applicationReservation->student_id;
                    $applianceStatus->academic_year = $applicationReservation->applicationInfo->applicationTimingInfo->academic_year;
                    $applianceStatus->interview_status = 'Pending Interview';
                    $applianceStatus->save();

                    $reservatoreMobile = $user->mobile;
                    $transactionRefId = $request->SaleOrderId;
                    $messageText = "You have successfully made your payment. Your application has been reserved.\nTransaction number: $transactionRefId \nSavior Schools";
                    $this->sendSMS($reservatoreMobile, $messageText);
                } else {
                    return redirect()->route('Applications.index')->withErrors(['Failed to verify application payment.']);
                }

                return redirect()->route('Applications.index')->with(['success' => "You have successfully paid for your application. Transaction number: $transactionRefId"]);
                break;
            case 17:
                return redirect()->route('Applications.index')->withErrors(['You refused to pay application amount!']);

                break;
            default:
                abort(419);
        }
    }

    public function verifyTuitionPayment(Request $request)
    {
        $transaction_id = \App\Models\Invoice::where('transaction_id', $request->RefId)->latest()->first();
        $user = User::find($transaction_id->user_id);

        if (Auth::loginUsingId($user->id)) {
            Session::put('id', $user->id);
        } else {
            abort(419);
        }

        switch ($request->ResCode) {
            case 0:
                $receipt = Payment::transactionId($request->RefId)->verify();

                if ($receipt) {
                    $transactionDetail = \App\Models\Invoice::where('transaction_id', $request->RefId)->latest()->first();

                    if (empty($transactionDetail)) {
                        abort(419);
                    }

                    $invoiceDescription = json_decode($transactionDetail->description, true);

                    $tuitionInvoiceDetails = TuitionInvoiceDetails::find($invoiceDescription['invoice_details_id']);
                    $tuitionInvoiceDetails->is_paid = 1;
                    $tuitionInvoiceDetails->invoice_id = $transactionDetail->id;
                    $tuitionInvoiceDetails->payment_details = $request->all();
                    $tuitionInvoiceDetails->save();

                    $tuitionInvoiceInfo = TuitionInvoices::find($tuitionInvoiceDetails->tuition_invoice_id);

                    $studentAppliance = StudentApplianceStatus::find($tuitionInvoiceInfo->appliance_id);
                    $studentAppliance->tuition_payment_status = 'Paid';
                    $studentAppliance->approval_status = 1;
                    $studentAppliance->save();

                    switch ($tuitionInvoiceInfo->payment_type) {
                        case 2:
                            $counter = 1;
                            $tuitionDetails = TuitionDetail::find(json_decode($tuitionInvoiceDetails->description,true)['tuition_details_id']);
                            $tuitionDetailsForTwoInstallments = json_decode($tuitionDetails->two_installment_payment, true);
                            $amountOfEachInstallments = str_replace(',', '', $tuitionDetailsForTwoInstallments['two_installment_each_installment_irr']);
                            while ($counter < 3) {
                                $newInvoice = new TuitionInvoiceDetails();
                                $newInvoice->tuition_invoice_id = $tuitionInvoiceDetails->tuition_invoice_id;
                                $newInvoice->amount = $amountOfEachInstallments;
                                $newInvoice->is_paid = 0;
                                $newInvoice->description = json_encode(['tuition_type' => 'Two Installment - Installment '.$counter], true);
                                $newInvoice->save();
                                $counter++;
                            }
                            break;
                        case 3:
                            $counter = 1;
                            $tuitionDetails = TuitionDetail::find(json_decode($tuitionInvoiceDetails->description,true)['tuition_details_id']);
                            $tuitionDetailsForFourInstallments = json_decode($tuitionDetails->four_installment_payment, true);
                            $amountOfEachInstallments = str_replace(',', '', $tuitionDetailsForFourInstallments['four_installment_each_installment_irr']);

                            while ($counter < 5) {
                                $newInvoice = new TuitionInvoiceDetails();
                                $newInvoice->tuition_invoice_id = $tuitionInvoiceDetails->tuition_invoice_id;
                                $newInvoice->amount = $amountOfEachInstallments;
                                $newInvoice->is_paid = 0;
                                $newInvoice->description = json_encode(['tuition_type' => 'Four Installment - Installment '.$counter], true);
                                $newInvoice->save();
                                $counter++;
                            }
                            break;
                    }

                    $reservatoreMobile = $user->mobile;
                    $transactionRefId = $request->SaleOrderId;
                    $messageText = "You have successfully paid tuition. \nTransaction number: $transactionRefId \nSavior Schools";
                    $this->sendSMS($reservatoreMobile, $messageText);
                } else {
                    return redirect()->route('dashboard')->withErrors(['Failed to verify application payment.']);
                }

                return redirect()->route('dashboard')->with(['success' => "You have successfully paid tuition amount. Transaction number: $transactionRefId"]);
                break;
            case 17:
                $transactionDetail = \App\Models\Invoice::where('transaction_id', $request->RefId)->latest()->first();
                $invoiceDescription = json_decode($transactionDetail->description, true);
                $tuitionInvoiceDetail = TuitionInvoiceDetails::find($invoiceDescription['invoice_details_id']);

                if ($tuitionInvoiceDetail) {
                    $tuitionInvoiceInfo = TuitionInvoices::find($tuitionInvoiceDetail->tuition_invoice_id);
                    $tuitionInvoiceInfo->delete();

                    $tuitionInvoiceDetail->delete();
                }

                $transactionDetail->delete();

                return redirect()->route('dashboard')->withErrors(['You refused to pay application amount!']);

                break;
            default:
                abort(419);
        }
    }
}
