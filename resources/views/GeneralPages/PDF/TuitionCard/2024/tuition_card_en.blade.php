<!DOCTYPE html>
<html lang="en">
@php
    use App\Models\Branch\ApplicationTiming;use App\Models\Catalogs\AcademicYear;use App\Models\Catalogs\Level;use App\Models\Finance\Tuition;use App\Models\Finance\TuitionInvoices;
    $evidencesInfo=json_decode($applianceStatus->evidences->informations,true);
    $applicationInformation=ApplicationTiming::join('applications','application_timings.id','=','applications.application_timing_id')
                                                ->join('application_reservations','applications.id','=','application_reservations.application_id')
                                                ->where('application_reservations.student_id',$applianceStatus->student_id)
                                                ->where('application_timings.academic_year',$applianceStatus->academic_year)->latest('application_reservations.id')->first();

    $levelInfo=Level::find($applicationInformation->level);
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: a4;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url(/build/export-images/bg-layer.jpg);

        }

        .bg-white {
            background-color: white;
        }

        .container {
            margin: 0 auto;
            padding: 0 1rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        header img {
            max-height: 100px;
        }

        header .title-description {
            text-align: center;
        }

        header .invoice-details {
            text-align: left;
        }

        section {
            margin: 20px 0;
            overflow: hidden;
            border: 2px solid;
            border-radius: 50px;
        }

        .contact-info > div {
            text-align: left;
        }

        .address {
            margin-top: 20px;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .contact-info {
            display: inline-flex;
            width: 100%;
        }

        .contact-info .name,
        .contact-info .contact-number {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .writing-rl {
            writing-mode: vertical-rl;
            transform: scale(-1);
            padding: 1.3rem 0;
            width: 60px;
        }

        .texthead {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .textbody {
            padding: 1rem;
            width: 100%;
        }


        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        #tuition_table tr th,
        tr td {
            padding: 1.2rem;
            border-left: 1px solid #9ddadf;
        }

        #tuition_table tr td {
            padding: 1.2rem;
            border-left: 1px solid;
            border-color: #9ddadf !important;;
        }

        #tuition_table tr th:first-child {
            border-left: 0;
        }

        #tuition_table tr td:first-child {
            border-left: 0;
        }

        #tuition_table tr td {
            border-top: 1px solid #9ddadf;
        }


        #table2 tr th,
        tr td {
            padding: 1.2rem;
            border-left: 1px solid #ffe753;
        }

        #table2 tr th:first-child {
            border-left: 0;
        }

        #table2 tr td:first-child {
            border-left: 0;
        }

        #table2 tr td {
            border-top: 1px solid #ffe753;
        }


        .font-bold {
            font-weight: 600;
        }

        .font-light {
            font-weight: 300;
        }


        .border-table {
            border: 1px solid;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        .title-section {
            background-color: #9ddadf;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border-top-right-radius: 20px;
            border-top-left-radius: 20px;
            color: rgb(0, 0, 0);
        }

        .table-container {
            display: flex;
            /* padding: 1rem; */
            justify-content: space-between;
        }

        .table-container table {
            padding: 1rem;
        }

        .bg-header {
            background-color: #e8f6f7;
        }

        .bg-blue {
            background-color: #9ddadf;
        }

        .bg-border-blue {
            border-color: #9ddadf !important;
        }

        .bg-yellow {
            background-color: #ffe753;
        }

        .bg-border-yellow {
            border-color: #ffe753 !important;
        }

        .text-white {
            color: white;
        }

        .p-1r {
            padding-right: 1rem;
        }

        .p-1l {
            padding-left: 1rem;
        }

        .m-0 {
            margin: 0;
        }

        .p-0 {
            padding: 0;
        }

        .mt-2rem {
            margin-top: 2rem;;
        }

        .w50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        #tuition_table th:nth-child(3),
        #tuition_table td:nth-child(3) {
            width: 150px;
            /* عرض دلخواه را اینجا تنظیم کنید */
        }

        .considerations {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .consideration-item {
            margin-bottom: 10px;
            position: relative;
            margin-left: 1.2em;
        }

        .consideration-item::before {
            content: "\2022";
            /* کد یونیکد دایره */
            color: #9ddadf;
            /* رنگ دایره‌ها */
            font-size: 50px;
            position: absolute;
            left: -20px;
            /* فاصله از سمت چپ */
            top: 48%;
            transform: translateY(-50%);
        }


        footer {
            background-color: #f0f0f0;
            padding: 20px;
            background-color: #e8f6f7;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            margin-left: 10px;
        }

        .footer-text {
            font-size: 16px;
            line-height: 1.5;
        }

        .font-norm {
            font-family: normal;
        }

        .table-v p > span {
            font-weight: 400;
        }

        .table-v p {
            font-weight: 600;
        }

    </style>
    <script>
        // window.print();
    </script>
    <title>Your Invoice</title>

</head>

<body class="container">

{{--Header--}}
<header class="bg-header">
    <div>
        <img src="/build/export-images/logo.png" alt="Logo">
    </div>
    <div class="title-description">
        <h1>Tuition Card</h1>
        <p>Monji Noor Education Institute</p>
    </div>
    <div class="invoice-details">
        <p class="font-bold">Invoice Number: <span class="font-light">INV-001</span></p>
        <p class="font-bold">Date: <span class="font-light">2024-02-12</span></p>
    </div>
</header>

{{--Education Center Details--}}
<section class="bg-border-blue bg-white table-v">
    <div class="flex">
        <div class="texthead bg-blue">
            <div class="writing-rl">
                <h5>Education Center Details</h5>
            </div>
        </div>
        <div class="textbody">
            <div class="contact-info">
                <div class="name">
                    <p>Name: <span>Monji Noor Education Institute</span></p>
                </div>
                <div class="contact-number">
                    <p>Contact Number: <span>+98 25 3770 4544</span></p>
                </div>
            </div>
            <div class="flex justify-between">
                <p>Postal Code: <span>37157-47748</span></p>
                <p>Registration Number: <span>60789562</span></p>
                <p>National ID: <span>60235789562</span></p>
            </div>
            <div class="address">
                <p>Address: <span>Savior International Schools, 8th Alley (at the end of Shah Hamzeh walkway), 5th Alley, Ammar Yaser Boulevard, Qom, I.R Iran.</span>
                </p>
            </div>
        </div>
    </div>
</section>

{{--Student Details--}}
<section class="bg-border-yellow bg-white table-v">
    <div class="flex">
        <div class="texthead bg-yellow">
            <div class="writing-rl">
                <h5>Student Details</h5>
            </div>
        </div>
        <div class="textbody">
            <div class="flex justify-between">
                <p>Full Name of Student:
                    <span>{{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->last_name_en }}</span>
                </p>
                <p>Passport Number: <span>{{ $evidencesInfo['student_passport_number'] }}</span></p>
                <p>Level of education: <span>{{$levelInfo->name}}</span></p>
            </div>
            <div class="flex justify-between">
                <p>Full Name of Parent/Guardian:
                    <span>{{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->last_name_en }}</span>
                </p>
                <p>Passport Number:
                    <span>{{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->passport_number }}</span>
                </p>
                <p>Student ID: <span>{{ $applianceStatus->student_id }}</span></p>
            </div>
            <div class="flex justify-between">
                <p>Country: <span>Afghanistan</span></p>
                <p>Contact Number: <span>{{ $applianceStatus->studentInformations->guardianInfo->mobile }}</span></p>
            </div>
        </div>
    </div>
</section>
@php
    $systemTuitionInfo=Tuition::join('tuition_details','tuitions.id','=','tuition_details.tuition_id')->where('tuition_details.level',$levelInfo->id)->first();
@endphp

{{--Tuition Table--}}
<div id="tuition_table" class="border-table bg-border-blue radius-table bg-white">
    <h3 class="title-section bg-blue p-1r m-0 radius-table">Tuition table for the academic
        year: {{ $applianceStatus->academicYearInfo->name }}</h3>
    <div class="table-container">
        <table>
            <tr>
                <th style="width: 15%">Currency of Payment</th>
                <th style="width: 25%">Full Payment tuition</th>
                <th style="width: 25%">Two Installment tuition</th>
                <th style="width: 25%">Four Installment tuition</th>
                <th style="width: 15%">Level</th>
            </tr>
            <tr>
                <td class="font-bold">Iranian Rial</td>
                <td>{{ json_decode($systemTuitionInfo->full_payment,true)['full_payment_irr'] }} IRR</td>
                <td>{{ json_decode($systemTuitionInfo->two_installment_payment,true)['two_installment_amount_irr'] }}
                    IRR
                </td>
                <td>{{ json_decode($systemTuitionInfo->four_installment_payment,true)['four_installment_amount_irr'] }}
                    IRR
                </td>
                <td>{{$levelInfo->name}}</td>
            </tr>
        </table>
    </div>
</div>

{{--Paid Tuition Table--}}
@php
    $myTuitionInfo=TuitionInvoices::with('invoiceDetails')->where('appliance_id',$applianceStatus->id)->first();
@endphp
<div style="margin-top: 1%" id="tuition_table" class="border-table bg-border-blue radius-table bg-white">
    <h3 class="title-section bg-blue p-1r m-0 radius-table">Your tuition</h3>
    <div class="table-container">
        <table>
            <tr>
                <th style="width: 25%">Payment Type</th>
                <th style="width: 25%">Total Payment Amount</th>
                <th style="width: 25%">Total Discounts (%)</th>
                <th style="width: 25%">Total Discounts (Amount)</th>
                <th style="width: 25%">Total Fee</th>
            </tr>
            <tr>
                <td class="font-bold">
                    @switch($myTuitionInfo->payment_type)
                        @case('1')
                            Full Payment
                            @break
                        @case('2')
                            Two installment
                            @break
                        @case('3')
                            Four Installment
                            @break
                        @case('4')
                            Full Payment With Advice
                            @break
                    @endswitch
                </td>
                <td>{{ json_decode($systemTuitionInfo->full_payment,true)['full_payment_irr'] }} IRR</td>
                <td>{{ json_decode($systemTuitionInfo->two_installment_payment,true)['two_installment_amount_irr'] }}
                    IRR
                </td>
                <td>{{ json_decode($systemTuitionInfo->four_installment_payment,true)['four_installment_amount_irr'] }}
                    IRR
                </td>
            </tr>
        </table>
    </div>
</div>


<div class="flex w-100">
    <div class="w-100 p-1r">
        <div id="table2" class="border-table bg-border-yellow radius-table mt-2rem bg-white">
            <h3 class="title-section bg-yellow p-1r m-0 radius-table">Payment Details</h3>
            <div class="table-container ">
                <table class="font-bold">
                    <tr>
                        <th>Installments</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Date received</th>
                        <th>Payment Method</th>
                    </tr>
                    @foreach($myTuitionInfo->invoiceDetails as $invoices)
                    <tr>
                        <td>15,000,000</td>
                        <td>15,000,000</td>
                        <td>35,000,000</td>
                    </tr>
                    @endforeach
                    <tr style="border-top: 1px solid #ffe753;">
                        <td class="font-bold">Total</td>
                        <td style="border: none;">15,000,000</td>

                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>

<div style="page-break-after: auto" class="Considerations">
    <h1>Considerations</h1>
    <ul class="considerations ">
        <li class="consideration-item">Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus ullam ea natus
            iusto architecto libero.
        </li>
        <li class="consideration-item">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint, neque!</li>
        <li class="consideration-item">Lorem, ipsum dolor sit amet consectetur adipisicing.</li>
        <li class="consideration-item">Lorem ipsum dolor sit amet.</li>
    </ul>
</div>
<footer class="mt-2rem">
    <div class="footer-text font-bold">
        I, {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->last_name_en }}
        , parent/guardian
        of {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->last_name_en }}
        , hereby agree to all rules and regulations of Monji Noor International Educational Institute.
    </div>
    <div class="footer-content font-bold">
        <div class="footer-text">Signature and Fingerprint of Parent/Guardian</div>
        <div class="footer-text">Signature and Stamp of Admissions</div>
        <div class="footer-text">Signature and Stamp of Finance</div>
    </div>
</footer>

</body>

</html>