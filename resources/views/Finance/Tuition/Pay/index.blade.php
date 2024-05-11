@php use Carbon\Carbon; @endphp
@extends('Layouts.panel')

@section('content')
    <div id="content" class="p-4 sm:ml-14 transition-all duration-300 bg-light-theme-color-base dark:bg-gray-800">
        <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
            <div class="grid grid-cols-1 gap-4 mb-4 mt-4 text-black dark:text-white">
                <h1 class="text-2xl font-medium">
                    Tuition Payment
                </h1>
            </div>
            @if (count($errors) > 0)
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                     role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <path
                                    d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                            </svg>
                        </div>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="font-bold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif( session()->has('success') )
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                     role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <path
                                    d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">{{ session()->get('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="lg:col-span-3 col-span-3 ">
                    <div class=" bg-white dark:bg-gray-800 dark:text-white p-8 rounded-lg mb-4">
                        <div class="col-span-1 gap-4 mb-4 text-black dark:text-white">
                            <h1 class="text-2xl font-medium"></h1>
                        </div>
                        <form id="pay-tuition" method="post" enctype="multipart/form-data"
                              action="{{ route('Tuitions.Pay') }}">
                            @csrf
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <p class="font-bold">Student ID: </p> {{ $studentApplianceStatus->student_id }}
                                </div>
                                <div>
                                    <p class="font-bold">Student
                                        Information: </p> {{ $studentApplianceStatus->studentInfo->generalInformationInfo->first_name_en }} {{ $studentApplianceStatus->studentInfo->generalInformationInfo->last_name_en }}
                                </div>
                                <div>
                                    <p class="font-bold">Academic
                                        Year: </p> {{ $studentApplianceStatus->academicYearInfo->name }}
                                </div>
                                <div>
                                    <p class="font-bold">Level For Educate: </p> {{ $applicationInfo->levelInfo->name }}
                                </div>
                            </div>
                            <hr>
                            <div
                                class="bg-teal-100 border-t-4 border-teal-500 rounded-b md:w-1/2 mb-4 mt-3 text-teal-900 px-4 py-3 shadow-md"
                                role="alert">
                                <div class="flex">
                                    <div class="py-1">
                                        <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20">
                                            <path
                                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold">Choose your payment type and method and click on the get
                                            invoice button.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid gap-6 mb-6 md:grid-cols-2 ">
                                <div class="grid gap-6 mb-6 md:grid-cols-3">
                                    <div>
                                        <label for="payment_type"
                                               class="block mb-2  font-bold text-gray-900 dark:text-white">
                                            Payment Type</label>
                                        <select id="payment_type" name="payment_type"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                title="Select student" required>
                                            <option selected disabled value="">Select type</option>
                                            <option value="1">Full payment</option>
                                            <option value="2">Two installments</option>
                                            <option value="3">Four installments</option>
                                            <option value="4">Full payment (With 30% Advice)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="payment_method"
                                               class="block mb-2  font-bold text-gray-900 dark:text-white">
                                            Payment Method</label>
                                        <select id="payment_method" name="payment_method"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                title="Select student" required>
                                            <option selected disabled value="">Select payment method</option>
                                            @foreach($paymentMethods as $paymentMethod)
                                                <option value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-8">
                                        <button type="button"
                                                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 get-invoice">
                                            Get Invoice
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $fullPayment=json_decode($tuition->full_payment,true);
                                        $fullPaymentAmount=str_replace(",", "", $fullPayment['full_payment_irr']);
                                        $fullPaymentAmountAdvance=((str_replace(",", "", $fullPayment['full_payment_irr']))*30)/100;
                                        $twoInstallmentPayment=json_decode($tuition->two_installment_payment,true);
                                        $twoInstallmentPaymentAmount=str_replace(",", "", $twoInstallmentPayment['two_installment_amount_irr']);
                                        $twoInstallmentPaymentAmountAdvance=((str_replace(",", "", $twoInstallmentPayment['two_installment_amount_irr']))*30)/100;
                                        $fourInstallmentPayment=json_decode($tuition->four_installment_payment,true);
                                        $fourInstallmentPaymentAmount=str_replace(",", "", $fourInstallmentPayment['four_installment_amount_irr']);
                                        $fourInstallmentPaymentAmountAdvance=((str_replace(",", "", $fourInstallmentPayment['four_installment_amount_irr']))*30)/100;
                                        $dateOfModified=Carbon::parse($studentApplianceStatus->updated_at);
                                        $dateOfDueAdvance=Carbon::parse($studentApplianceStatus->updated_at)->addHours(72);
                                        $allDiscountPercentages=$discountPercentages+$familyDiscount;
                                        if ($allDiscountPercentages>40){
                                            $allDiscountPercentages=40;
                                        }
                                    @endphp
                                </div>
                                <div class="overflow-x-auto">
                                    {{--                                    Full payment divs--}}
                                    <div id="full-payment-div" hidden="">
                                        <div id="full-payment-online" hidden=""
                                             class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                             role="alert">
                                            <div class="flex">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold">You have to pay the entire fee through the
                                                        online payment portal (Iranian) within the next 72 hours
                                                        .<br> After payment, you will be
                                                        It will take you to the invoices page.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="full-payment-online" hidden=""
                                         class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div class="flex">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">You must pay the total fee through the online
                                                    (Iranian) payment portal.<br> After payment, you will be
                                                    transferred to the invoices page.</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                    2 installments divs--}}
                                    <div id="installment2-div" hidden="">
                                        <div
                                            class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                            role="alert">
                                            <div class="flex">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold">In the installment method, you are allowed to
                                                        pay the first row of the table below within the next 72 hours.
                                                        After payment, you can pay other rows separately in the payments
                                                        section.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead
                                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="p-4">
                                                        <div class="flex items-center">
                                                            #
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Date Created
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Due Date
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Due Amount
                                                    </th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        1
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{$dateOfDueAdvance}}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format($twoInstallmentPaymentAmountAdvance) }}
                                                    </td>
                                                </tr>
                                                @php
                                                $twoEachInstallments=str_replace(',','',$twoInstallmentPayment['two_installment_each_installment_irr']);
                                                @endphp
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        2
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $twoInstallmentPayment['date_of_installment1_two'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($twoEachInstallments)-(($twoEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        3
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $twoInstallmentPayment['date_of_installment2_two'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($twoEachInstallments)-(($twoEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="installment2-online" hidden=""
                                         class="bg-yellow-100 border-t-4 border-yellow-500 rounded-b text-yellow-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div class="flex">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">You must pay your first installment through the
                                                    direct banking portal. <br>
                                                    After payment, you will be automatically redirected to your payment
                                                    list page.<br>
                                                    If you wish, you can pay your installments ahead of time on that
                                                    page.</p>
                                            </div>
                                        </div>
                                        <div class="flex mt-4">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">When paying, note that the deposit amount is the
                                                    same amount as your first installment.<br>
                                                    In case of discrepancy, prevent the payment and inform the financial
                                                    unit of your school.</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                    4 installments divs--}}
                                    <div id="installment4-div" hidden="">
                                        <div
                                            class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                            role="alert">
                                            <div class="flex">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold">In the installment method, you are allowed to
                                                        pay the first row of the table below within the next 72 hours.
                                                        After payment, you can pay other rows separately in the payments
                                                        section.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead
                                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="p-4">
                                                        <div class="flex items-center">
                                                            #
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Date Created
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Due Date
                                                    </th>
                                                    <th scope="col" class="px-6 py-1 text-center">
                                                        Due Amount
                                                    </th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        1
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{$dateOfDueAdvance}}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format($fourInstallmentPaymentAmountAdvance) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $fourEachInstallments=str_replace(',','',$fourInstallmentPayment['four_installment_each_installment_irr']);
                                                @endphp
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        2
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $fourInstallmentPayment['date_of_installment1_four'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($fourEachInstallments)-(($fourEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        3
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $fourInstallmentPayment['date_of_installment2_four'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($fourEachInstallments)-(($fourEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        4
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $fourInstallmentPayment['date_of_installment3_four'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($fourEachInstallments)-(($fourEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <td class="w-4 p-4 text-center">
                                                        5
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $dateOfModified }}
                                                    </td>
                                                    <td class="w-4 p-4 text-center">
                                                        {{ $fourInstallmentPayment['date_of_installment4_four'] }}
                                                    </td>
                                                    <td class="w-20 p-4 text-center">
                                                        IRR {{ number_format(($fourEachInstallments)-(($fourEachInstallments*$allDiscountPercentages)/100)) }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="installment4-online" hidden=""
                                         class="bg-yellow-100 border-t-4 border-yellow-500 rounded-b text-yellow-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div class="flex">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">You must pay your first installment through the
                                                    direct banking portal. <br>
                                                    After payment, you will be automatically redirected to your payment
                                                    list page.<br>
                                                    If you wish, you can pay your installments ahead of time on that
                                                    page.</p>
                                            </div>
                                        </div>
                                        <div class="flex mt-4">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">When paying, note that the deposit amount is the
                                                    same amount as your first installment.<br>
                                                    In case of discrepancy, prevent the payment and inform the financial
                                                    unit of your school.</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                    Full payment with advice divs--}}
                                    <div id="full-payment-with-advice-div" hidden="">
                                        <div id="full-payment-with-advice-online" hidden=""
                                             class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                             role="alert">
                                            <div class="flex">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold">You have to pay 30% of the total tuition fee and you have to pay the remaining 70% by the end of September.
                                                        <br> After payment, you will be
                                                        It will take you to the invoices page.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="full-payment-with-advice-online" hidden=""
                                         class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div class="flex">
                                            <div class="py-1">
                                                <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path
                                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold">You have to pay 30% of the total tuition fee and you have to pay the remaining 70% by the end of September.<br> After payment, you will be
                                                    transferred to the invoices page.</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                    File divs--}}
                                    <div id="offline-full-payment-div" hidden=""
                                         class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div>
                                            <div class="flex mb-4">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    You must deposit
                                                    total fee amount using one of the following methods (bank
                                                    account number, bank card
                                                    number or Shaba number) and upload the image of your bank slip
                                                    from the box
                                                    below.
                                                </div>
                                            </div>
                                            <div>
                                                @foreach($paymentMethods as $methods)
                                                    @if(empty($methods->description))
                                                        @continue
                                                    @endif
                                                    @php
                                                        $descriptions = null;
                                                        if ($methods->description) {
                                                            $descriptions = json_decode($methods->description, true);
                                                        }
                                                    @endphp

                                                    @if ($descriptions)
                                                        @foreach($descriptions as $title => $description)
                                                            <label
                                                                class="block mb-2 font-bold text-gray-900 dark:text-white">
                                                                {{ $title }}: {{ $description }}
                                                            </label>
                                                        @endforeach
                                                    @else
                                                        <p>No descriptions available</p>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="grid gap-6 mb-6 md:grid-cols-1">
                                            <label
                                                class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_full_payment1">Select your bank slip file 1
                                                (Required)</label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400"
                                                name="document_file_full_payment1" id="document_file_full_payment1"
                                                type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">
                                            <img class="w-full h-auto" id="image_preview_full_payment1" src=""
                                                 alt="Preview Image"
                                                 style="display:none; ">
                                            <label
                                                class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_full_payment2">Select your bank slip file 2
                                                (Optional)</label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400"
                                                name="document_file_full_payment2" id="document_file_full_payment2"
                                                type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">
                                            <img class="w-full h-auto" id="image_preview_full_payment2" src=""
                                                 alt="Preview Image"
                                                 style="display:none; ">
                                            <label
                                                class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_full_payment3">Select your bank slip file 3
                                                (Optional)</label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400"
                                                name="document_file_full_payment3" id="document_file_full_payment3"
                                                type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">
                                            <img class="w-full h-auto" id="image_preview_full_payment3" src=""
                                                 alt="Preview Image"
                                                 style="display:none; ">
                                            <div class="info mb-5">
                                                <div class="dark:text-white font-medium mb-1">File requirements:
                                                </div>
                                                <div class="dark:text-gray-400 font-normal text-sm pb-1">Ensure that
                                                    these
                                                    requirements
                                                    are met:
                                                </div>
                                                <ul class="text-gray-500 dark:text-gray-400 text-xs font-normal ml-4 space-y-1">
                                                    <li>
                                                        The files must be in this format: png, jpg, jpeg, pdf, bmp
                                                    </li>
                                                    <li>
                                                        Maximum size: 5 MB
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="offline-installment-div" hidden=""
                                         class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                                         role="alert">
                                        <div>
                                            <div class="flex mb-4">
                                                <div class="py-1">
                                                    <svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    You must deposit the advance payment amount (the amount of the first
                                                    row) using one of the following methods (bank account number, bank
                                                    card number or Shaba number) and upload the image of your bank slip
                                                    from the box below.
                                                    <p class="font-bold text-red-600">Avoid depositing more than the
                                                        first row.</p>
                                                </div>
                                            </div>
                                            <div>
                                                @foreach($paymentMethods as $methods)
                                                    @if(empty($methods->description))
                                                        @continue
                                                    @endif
                                                    @php
                                                        $descriptions = null;
                                                        if ($methods->description) {
                                                            $descriptions = json_decode($methods->description, true);
                                                        }
                                                    @endphp

                                                    @if ($descriptions)
                                                        @foreach($descriptions as $title => $description)
                                                            <label
                                                                class="block mb-2 font-bold text-gray-900 dark:text-white">
                                                                {{ $title }}: {{ $description }}
                                                            </label>
                                                        @endforeach
                                                    @else
                                                        <p>No descriptions available</p>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="grid gap-6 mb-6 md:grid-cols-1">
                                            <label
                                                class="block mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_offline_installment1">Select your bank slip
                                                file 1 (Required)</label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400 document_file"
                                                name="document_file_offline_installment1"
                                                id="document_file_offline_installment1" type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">

                                            <img class="w-full h-auto image_preview"
                                                 id="image_preview_offline_installment1" src="" alt="Preview Image"
                                                 style="display:none; ">
                                            <label
                                                class="block mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_offline_installment2">Select your bank slip 2 file
                                                (Optional)
                                            </label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400 document_file"
                                                name="document_file_offline_installment2"
                                                id="document_file_offline_installment2" type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">
                                            <img class="w-full h-auto image_preview"
                                                 id="image_preview_offline_installment2" src="" alt="Preview Image"
                                                 style="display:none; ">
                                            <label
                                                class="block mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                                for="document_file_offline_installment3">Select your bank slip 3 file
                                                (Optional)
                                            </label>
                                            <input
                                                class="mb-4 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-600 dark:border-gray-600 dark:placeholder-gray-400 document_file"
                                                name="document_file_offline_installment3"
                                                id="document_file_offline_installment3" type="file"
                                                accept=".png,.jpg,.jpeg,.pdf,.bmp">
                                            <img class="w-full h-auto image_preview"
                                                 id="image_preview_offline_installment3" src="" alt="Preview Image"
                                                 style="display:none; ">
                                            <div class="info mb-5">
                                                <div class="dark:text-white font-medium mb-1">File requirements:
                                                </div>
                                                <div class="dark:text-gray-400 font-normal text-sm pb-1">Ensure that
                                                    these
                                                    requirements
                                                    are met:
                                                </div>
                                                <ul class="text-gray-500 dark:text-gray-400 text-xs font-normal ml-4 space-y-1">
                                                    <li>
                                                        The files must be in this format: png, jpg, jpeg, pdf, bmp
                                                    </li>
                                                    <li>
                                                        Maximum size: 5 MB
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <div hidden="" id="full-payment-invoice">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <tbody>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Fee:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format($fullPaymentAmount) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Discount:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format((($fullPaymentAmount-$fullPaymentAmountAdvance)*$discountPercentages)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Family Discount:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format((($fullPaymentAmount-$fullPaymentAmountAdvance)*$familyDiscount)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Total Fee:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format($fullPaymentAmount-(((($fullPaymentAmount-$fullPaymentAmountAdvance)*$familyDiscount)/100)+((($fullPaymentAmount-$fullPaymentAmountAdvance)*$discountPercentages)/100))) }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div hidden="" id="installment2-payment-invoice">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <tbody>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Fee:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format($twoInstallmentPaymentAmount) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Discount:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format((($twoInstallmentPaymentAmount-$twoInstallmentPaymentAmountAdvance)*$discountPercentages)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Family Discount:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format((($twoInstallmentPaymentAmount-$twoInstallmentPaymentAmountAdvance)*$familyDiscount)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Total Fee:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format($twoInstallmentPaymentAmount-(((($twoInstallmentPaymentAmount-$twoInstallmentPaymentAmountAdvance)*$familyDiscount)/100)+((($twoInstallmentPaymentAmount-$twoInstallmentPaymentAmountAdvance)*$discountPercentages)/100))) }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div hidden="" id="installment4-payment-invoice">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <tbody>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Fee:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format($fourInstallmentPaymentAmount) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Discount:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format((($fourInstallmentPaymentAmount-$fourInstallmentPaymentAmountAdvance)*$discountPercentages)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Family Discount:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format((($fourInstallmentPaymentAmount-$fourInstallmentPaymentAmountAdvance)*$familyDiscount)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Total Fee:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format($fourInstallmentPaymentAmount-(((($fourInstallmentPaymentAmount-$fourInstallmentPaymentAmountAdvance)*$familyDiscount)/100)+((($fourInstallmentPaymentAmount-$fourInstallmentPaymentAmountAdvance)*$discountPercentages)/100))) }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div hidden="" id="full-payment-with-advice-invoice">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <tbody>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Fee:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format($fullPaymentAmount) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Discount:
                                                </th>
                                                <td class="p-4 w-56 items-center">
                                                    IRR {{ number_format((($fullPaymentAmount-$fullPaymentAmountAdvance)*$discountPercentages)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Family Discount:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format((($fullPaymentAmount-$fullPaymentAmountAdvance)*$familyDiscount)/100) }}
                                                </td>
                                            </tr>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="col" class="p-4">
                                                    Total Fee:
                                                </th>
                                                <td class="w-56 p-4">
                                                    IRR {{ number_format($fullPaymentAmount-(((($fullPaymentAmount-$fullPaymentAmountAdvance)*$familyDiscount)/100)+((($fullPaymentAmount-$fullPaymentAmountAdvance)*$discountPercentages)/100))) }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" value="{{$studentApplianceStatus->student_id}}" name="student_id">
                            <input type="hidden" value="{{$studentApplianceStatus->id}}" name="appliance_id">
                            <div id="accept-div" class="flex justify-between items-start mb-3 hidden">
                                <div class="flex items-center h-5">
                                    <input id="accept" type="checkbox" name="accept" required
                                           class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300  dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800"
                                    >
                                    <label for="accept" class="ml-2 text-sm font-medium">I have read all the contents of
                                        the page and I accept it.</label>
                                </div>
                            </div>
                            <button type="submit" id="payment-button" hidden=""
                                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Payment
                            </button>
                            <a href="{{ url()->previous() }}">
                                <button type="button"
                                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                    Back
                                </button>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
