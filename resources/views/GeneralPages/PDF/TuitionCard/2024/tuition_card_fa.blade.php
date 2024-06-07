<!DOCTYPE html>
<html dir="rtl" lang="en">
@php
    use App\Models\Branch\ApplicationTiming;use App\Models\Branch\Evidence;use App\Models\Branch\Interview;use App\Models\Branch\StudentApplianceStatus;use App\Models\Catalogs\AcademicYear;use App\Models\Catalogs\Level;use App\Models\Country;use App\Models\Finance\DiscountDetail;use App\Models\Finance\Tuition;use App\Models\Finance\TuitionInvoices;use App\Models\StudentInformation;
    use \Morilog\Jalali\Jalalian;

    $evidencesInfo=json_decode($applianceStatus->evidences->informations,true);
    $applicationInformation=ApplicationTiming::join('applications','application_timings.id','=','applications.application_timing_id')
                                                ->join('application_reservations','applications.id','=','application_reservations.application_id')
                                                ->where('application_reservations.student_id',$applianceStatus->student_id)
                                                ->where('application_timings.academic_year',$applianceStatus->academic_year)->latest('application_reservations.id')->first();
    $levelInfo=Level::find($applicationInformation->level);
    switch (true){
        case (strstr($levelInfo->name,'Grade')):
            $levelName=str_replace('Grade','پایه',$levelInfo->name);
            break;
        case (strstr($levelInfo->name,'Kindergarten')):
            $levelName=str_replace('Kindergarten','پیش دبستانی',$levelInfo->name);
            break;
    }

    $systemTuitionInfo=Tuition::join('tuition_details','tuitions.id','=','tuition_details.tuition_id')->where('tuition_details.level',$levelInfo->id)->first();
    $myTuitionInfo=TuitionInvoices::with('invoiceDetails')->where('appliance_id',$applianceStatus->id)->first();
    $totalAmount=0;

    foreach($myTuitionInfo->invoiceDetails as $invoices){
        $totalAmount=$invoices->amount+$totalAmount;
    }

    $paymentAmount=null;
    switch ($myTuitionInfo->payment_type){
        case 1:
        case 4:
            $paymentAmount=str_replace(',','',json_decode($systemTuitionInfo->full_payment,true)['full_payment_irr']);
            break;
        case 2:
            $paymentAmount=str_replace(',','',json_decode($systemTuitionInfo->two_installment_payment,true)['two_installment_amount_irr']);
            break;
        case 3:
            $paymentAmount=str_replace(',','',json_decode($systemTuitionInfo->four_installment_payment,true)['four_installment_amount_irr']);
            break;
    }

    //Discounts
    $interviewForm=Interview::where('application_id',$applicationInformation->application_id)->where('interview_type',3)->latest()->first();
    if (!isset(json_decode($interviewForm->interview_form,true)['discount'])){
        $discounts=[];
    }else{
        $discounts=json_decode($interviewForm->interview_form,true)['discount'];
    }
    $discounts=DiscountDetail::whereIn('id',$discounts)->get();

    $fatherNationality=Country::find($evidencesInfo['father_nationality']);

    $academicYearInfo=AcademicYear::with('schoolInfo')->find($applicationInformation->academic_year);
    $schoolAddress=$academicYearInfo->schoolInfo->address_fa;
    $schoolBranch=$academicYearInfo->schoolInfo->name;
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4;
        }

        @media print {
            @page {
                scale: 0.6; /* 60% مقیاس پرینت */
            }
        }

        @font-face {
            font-family: IRANSansX;
            src: url('../../build/Fonts/IRANSansX-DemiBold.woff') format('woff');
        }

        @font-face {
            font-family: IRANSansX;
            src: url('../../build/Fonts/IRANSansX-Light.woff') format('woff');
        }

        @font-face {
            font-family: IRANSansX;
            src: url('../../build/Fonts/IRANSansX-Medium.woff') format('woff');
        }

        body {
            font-family: IRANSansX, Arial, sans-serif;
            width: 20cm;
            height: 21cm;
        }

        .bg-white {
            background-color: white;
        }

        .container {
            margin: 0 auto;
            padding: 0 1rem;
        }

        span {
            font-weight: bold !important;
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
            text-align: right;
        }

        section {
            margin: 10px 0;
            overflow: hidden;
            border: 2px solid;
            border-radius: 50px;
        }

        .contact-info > div {
            text-align: right;
        }

        /*.address {*/
        /*    margin-top: 20px;*/
        /*}*/

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
            padding: 0.5rem 0;
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

        .textbody p {
            margin-top: 2px;
            margin-bottom: 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            /*padding: 10px;*/
            text-align: center;
        }

        #tuition_table tr th,
        tr td {
            /*padding: 1.2rem;*/
            border-left: 1px solid #9ddadf;
        }

        #tuition_table tr td {
            /*padding: 1.2rem;*/
            border-left: 1px solid;
            border-color: #9ddadf !important;;
        }

        #tuition_table tr th:first-child {
            border-left: 1px solid;
            border-color: #9ddadf !important;;
        }

        #tuition_table tr td:first-child {
            border-left: 1px solid;
            border-color: #9ddadf !important;;
        }

        #tuition_table tr td {
            border-top: 1px solid #9ddadf;
        }


        #table2 {
            width: 100%;
        }

        #table2 tr th,
        tr td {
            /*padding: 1.2rem;*/
            border-left: 1px solid #ffe753;
        }

        #table2 tr th:first-child {
            border-left: 0;
            border-left: 1px solid #ffe753;
        }

        #table2 tr td:first-child {
            border-left: 0;
            border-left: 1px solid #ffe753;
        }

        #table2 tr td {
            border-top: 1px solid #ffe753;
        }


        .font-bold {
            font-weight: bold;
        }

        .font-light {
            font-weight: normal;
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
            /*padding: 1rem;*/
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

        .mt-2rem {
            margin-top: 0.5rem;;
        }

        .w-100 {
            width: 100%;
        }

        #tuition_table th:nth-child(3),
        #tuition_table td:nth-child(3) {
            width: 150px;
        }

        .considerations {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .consideration-item {
            margin-bottom: 10px;
            position: relative;
            margin-right: 1.2em;
        }

        .consideration-item::before {
            content: "\2022";
            color: #9ddadf;
            font-size: 20px;
            position: absolute;
            right: -20px;
            top: 48%;
            transform: translateY(-50%);
        }


        footer {
            padding: 20px;
            background-color: #e8f6f7;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            /*margin-top: 1.5rem;*/
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
            /*font-weight: 600;*/
        }

        .ltr-text {
            direction: ltr !important;
            unicode-bidi: embed;
            text-align: left;
        }

        .payment-details-table td {
            padding: 5px;
            font-weight: normal;
        }
    </style>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            var spans = document.querySelectorAll('span');

            spans.forEach(function (span) {
                span.classList.add('ltr-text');
            });
        });


        // window.print();

        function setPrintScale() {
            if (window.matchMedia('print').matches) {
                var scale = 0.6; // 60%
                document.body.style.transform = 'scale(' + scale + ')';
                document.body.style.transformOrigin = 'top';
            }
        }

        setPrintScale();
    </script>
    <title>کارت شهریه</title>
</head>
<body class="container">

{{--Header--}}
<header class="bg-header">
    <div>
        <img src="/build/export-images/logo.png" alt="Logo">
    </div>
    <div class="title-description">
        <h1>{{ __('translated_fa.Tuition Card') }}</h1>
        <p>{{ __('translated_fa.Monji Noor Education Institute') }}</p>
    </div>
    <div class="invoice-details">
        <p class="font-bold">{{ __('translated_fa.Invoice Number') }}: <span
                class="font-light">{{ $myTuitionInfo->id }}</span></p>
        <p class="font-bold">{{ __('translated_fa.Date') }}: <span
                class="font-light">{{ Jalalian::forge('today')->format('%A, %d %B %Y') }}</span></p>
    </div>
</header>

{{--Education Center Details--}}
<section class="bg-border-blue bg-white table-v">
    <div class="flex">
        <div class="texthead bg-blue">
            <div class="writing-rl">
                <h5>{{ __('translated_fa.Education Center Details') }}</h5>
            </div>
        </div>
        <div class="textbody">
            <div class="contact-info">
                <div class="name">
                    <p>{{ __('translated_fa.Name') }}:
                        <span class="font-bold">{{ __('translated_fa.Monji Noor Education Institute') }}</span></p>
                </div>
                <div class="contact-number">
                    <p>{{ __('translated_fa.Contact Number') }}: <span>+98 25 3770 4544</span></p>
                </div>
            </div>
            <div class="contact-info">
                <div class="name">
                    <p>{{ __('translated_fa.Branch') }}: <span>{{$schoolBranch}}</span></p>
                </div>
                <div class="name">
                    <p>{{ __('translated_fa.Academic Year') }}: <span>{{$academicYearInfo->name}}</span></p>
                </div>
            </div>
            <div class="flex justify-between">
                <p>{{ __('translated_fa.Postal Code') }}: <span>37156-57571</span></p>
                <p>{{ __('translated_fa.Registration Number') }}: <span>60789562</span></p>
                <p>{{ __('translated_fa.National ID') }}: <span>14011156661</span></p>
            </div>
            <div class="address">
                <p>{{ __('translated_fa.Address') }}: <span style="font-size: small !important;">
                       {{$schoolAddress}}
                    </span>
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
                <h5>{{ __('translated_fa.Student Details') }}</h5>
            </div>
        </div>
        <div class="textbody">
            <div class="flex justify-between">
                <p>{{ __('translated_fa.Full Name of Student') }}:
                    <span>{{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->last_name_en }}</span>
                </p>
                <p>{{ __('translated_fa.Passport Number') }}:
                    <span>{{ $evidencesInfo['student_passport_number'] }}</span></p>
                <p style="white-space: nowrap">{{ __('translated_fa.Level of education') }}: <span style="white-space: nowrap">{{$levelName}}</span></p>
            </div>
            <div class="flex justify-between">
                <p>{{ __('translated_fa.Full Name of Parent/Guardian') }}:
                    <span>{{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->last_name_en }}</span>
                </p>
                <p>{{ __('translated_fa.Passport Number') }}:
                    <span>{{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->passport_number }}</span>
                </p>
                <p>{{ __('translated_fa.Student ID') }}: <span>{{ $applianceStatus->student_id }}</span></p>
            </div>
            <div class="flex justify-between">
                <p>{{ __('translated_fa.Country') }}: <span>{{$fatherNationality->en_short_name}}</span></p>
                <p>{{ __('translated_fa.Contact Number') }}:
                    <span>{{ $applianceStatus->studentInformations->guardianInfo->mobile }}</span></p>
            </div>
        </div>
    </div>
</section>

{{--Paid Tuition Table--}}
<section id="tuition_table" class="border-table bg-border-blue radius-table bg-white">
    <div class="flex bg-blue">
        <div class="texthead bg-blue">
            <div class="writing-rl">
                <h5>{{ __('translated_fa.Your tuition') }}</h5>
            </div>
        </div>
        <div class="table-container bg-white">
            <table style="width: 100%">
                <tr>
                    <th style="width: 10%;height: 0">{{ __('translated_fa.Payment Type') }}</th>
                    <th style="width: 15%">{{ __('translated_fa.Total Payment Amount') }}</th>
                    <th style="width: 15%">{{ __('translated_fa.Total Discounts') }} (درصد)</th>
                    <th style="width: 15%">{{ __('translated_fa.Total Discounts') }} ({{ __('translated_fa.Amount') }}
                        )
                    </th>
                    <th style="width: 15%">{{ __('translated_fa.Total Fee') }}</th>
                </tr>
                <tr style="">
                    <td style="white-space: nowrap;height: 0">
                        @switch($myTuitionInfo->payment_type)
                            @case('1')
                                {{ __('translated_fa.Full Payment') }}
                                @break
                            @case('2')
                                {{ __('translated_fa.Two Installment') }}
                                @break
                            @case('3')
                                {{ __('translated_fa.Four Installment') }}
                                @break
                            @case('4')
                                {{ __('translated_fa.Full Payment With Advance') }}
                                @break
                        @endswitch
                    </td>
                    <td style="white-space: nowrap">{{ number_format($paymentAmount) }} </td>
                    <td>{{ $allDiscounts }}</td>
                    <td>{{ number_format((($paymentAmount*$allDiscounts)/100)+$allFamilyDiscounts->discount_price) }}</td>
                    <td style="white-space: nowrap">{{ number_format($totalAmount) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</section>

{{--Payment Details--}}
<div class="flex w-100">
    <div id="table2" class="border-table bg-border-yellow radius-table mt-2rem bg-white">
        <div class="flex">
            <div class="texthead bg-yellow">
                <div class="writing-rl">
                    <h5>{{ __('translated_fa.Payment Details') }}</h5>
                </div>
            </div>
            <div class="table-container ">
                <table class="payment-details-table font-bold">
                    <tr>
                        <th>{{ __('translated_fa.Type') }}</th>
                        <th>{{ __('translated_fa.Amount') }}</th>
                        <th style="white-space: nowrap">{{ __('translated_fa.Due Date') }}</th>
                        <th>{{ __('translated_fa.Date received') }}</th>
                        <th>{{ __('translated_fa.Payment Method') }}</th>
                        @php $paidAmount = $debt = 0 @endphp
                    </tr>
                    @foreach($myTuitionInfo->invoiceDetails as $key=>$invoices)
                        @php
                            $invoiceDetailsDescription=json_decode($invoices->description,true);
                            $tuitionType=$invoiceDetailsDescription['tuition_type'];
                            $dueType=null;
                            if (strstr($tuitionType,'Four') and !strstr($tuitionType,'Advance')){
                                $dueType='Four';
                                $dueDates=json_decode($systemTuitionInfo->four_installment_payment,true);
                            }
                            if (strstr($tuitionType,'Two') and !strstr($tuitionType,'Advance')){
                                $dueType='Two';
                                $dueDates=json_decode($systemTuitionInfo->two_installment_payment,true);
                            }
                            if (strstr($tuitionType,'Full') and strstr($tuitionType,'Advance') and strstr($tuitionType,'Installment')){
                                $dueType='Full';
                            }
                        @endphp
                        <tr style="padding: 4px">
                            <td style="white-space: nowrap">
                                @switch($tuitionType)
                                    @case('Two Installment Advance')
                                        پیش پرداخت
                                        @break
                                    @case('Two Installment - Installment 1')
                                        قسط اول
                                        @break
                                    @case('Two Installment - Installment 2')
                                        قسط دوم
                                        @break
                                    @case('Four Installment Advance')
                                        پیش پرداخت
                                        @break
                                    @case('Four Installment - Installment 1')
                                        قسط اول
                                        @break
                                    @case('Four Installment - Installment 2')
                                        قسط دوم
                                        @break
                                    @case('Four Installment - Installment 3')
                                        قسط سوم
                                        @break
                                    @case('Four Installment - Installment 4')
                                        قسط چهارم
                                        @break
                                    @case('Full Payment With Advance - Installment')
                                        پرداخت دوم
                                        @break
                                    @case('Full Payment With Advance')
                                        پرداخت اول
                                        @break
                                    @case('Full Payment')
                                        پرداخت کامل
                                        @break
                                @endswitch
                            </td>
                            <td style="white-space: nowrap">{{ number_format($invoices->amount) }} </td>
                            <td style="white-space: nowrap">
                                @switch ($dueType)
                                    @case('Four')
                                        @php
                                            $jalaliDate = Jalalian::fromDateTime($dueDates["date_of_installment".$key."_four"]);
                                            $formattedJalaliDate = $jalaliDate->format('Y/m/d');
                                        @endphp
                                        {{ $formattedJalaliDate }}
                                        @break
                                    @case('Two')
                                        @php
                                            $jalaliDate = Jalalian::fromDateTime($dueDates["date_of_installment".$key."_two"]);
                                            $formattedJalaliDate = $jalaliDate->format('Y/m/d');
                                        @endphp
                                        {{ $formattedJalaliDate }}
                                        @break
                                    @case('Full')
                                        @php
                                            $jalaliDate = Jalalian::fromDateTime($invoices->date_of_payment);
                                            $endOfShahrivar = Jalalian::fromFormat('Y/m/d', $jalaliDate->getYear() . '/06/31');
                                            $formattedJalaliDate = $endOfShahrivar->format('Y/m/d');
                                            echo $formattedJalaliDate;
                                        @endphp
                                        @break
                                    @default
                                        -
                                @endswitch
                            </td>
                            <td class="ltr-text" style="white-space: nowrap;text-align: center">
                                @if(isset($invoices->date_of_payment))
                                    @php
                                        $jalaliDate = Jalalian::fromDateTime($invoices->date_of_payment);
                                        echo $formattedJalaliDate = $jalaliDate->format('Y/m/d H:i:s');
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                            <td style="white-space: nowrap">
                                @if(isset($invoices->paymentMethodInfo->name))
                                    @switch($invoices->paymentMethodInfo->name)
                                        @case('Offline Payment')
                                            پرداخت آفلاین
                                            @break
                                        @default
                                            پرداخت آنلاین - درگاه به پرداخت ملت
                                    @endswitch
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($invoices->date_of_payment!=null)
                            @php
                                $paidAmount=$invoices->amount+$paidAmount;
                            @endphp
                        @endif
                        @if($invoices->date_of_payment==null)
                            @php
                                $debt=$totalAmount-$paidAmount;
                            @endphp
                        @endif
                    @endforeach
                    <tr style="border-top: 1px solid #ffe753;white-space: nowrap">
                        <td class="font-bold">{{ __('translated_fa.Total') }}</td>
                        <td>{{ number_format($totalAmount) }} </td>
                        <td class="font-bold">{{ __('translated_fa.Paid Amount') }}</td>
                        <td>{{ number_format($paidAmount) }} </td>
                        <td class="font-bold">{{ __('translated_fa.Debt') }}</td>
                        <td>{{ number_format($debt) }} </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{--Considerations--}}
<div style="page-break-after: auto" class="Considerations">
    <h3>{{ __('translated_fa.Considerations') }}</h3>
    <ul class="considerations ">
        <li class="consideration-item font-bold">
            <u>
                تمامی مبالغ به ریال می باشند.
            </u>
        </li>
        @if($discounts->isNotEmpty())
            <li class="consideration-item font-bold">
                تخفیف ها
            </li>
            @foreach($discounts as $discount)
                <li class="consideration-item">
                    {{ $discount->id." - " }}
                </li>
            @endforeach
        @endif
        @if($allFamilyDiscounts->discount_price>0)
            <li class="consideration-item font-bold">
                {{ __('translated_fa.Included Family Discounts') }}
                ({{number_format($allFamilyDiscounts->discount_price)}} ریال)
            </li>
        @endif
    </ul>
</div>

{{--Footer--}}
<footer class="mt-2rem">
    <div class="footer-text ">
        اینجانب
        {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->guardianInfo->generalInformationInfo->last_name_en }}
        ،
        والدین / قیم
        {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->first_name_en }} {{ $applianceStatus->studentInformations->studentInfo->generalInformationInfo->last_name_en }}
        ،
        بدین وسیله با کلیه قوانین و مقررات موسسه آموزشی بین المللی منجی نور موافقت می نمایم.
    </div>
    <div class="footer-content ">
        <div class="footer-text">{{ __('translated_fa.Signature and Fingerprint of Parent/Guardian') }}</div>
        <div class="footer-text">{{ __('translated_fa.Signature and Stamp of Admissions') }}</div>
        <div class="footer-text">{{ __('translated_fa.Signature and Stamp of Finance') }}</div>
    </div>
</footer>

</body>

</html>
