@extends('Layouts.panel')

@section('content')
    <div id="content" class="p-4 md:ml-14 transition-all duration-300 bg-light-theme-color-base dark:bg-gray-800">
        <div class="p-4 rounded-lg dark:border-gray-700 mt-20 ">
            <div class="grid grid-cols-1 gap-4 mb-4">
                <h1 class="text-3xl font-semibold text-black dark:text-white ">All Students Status</h1>
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <div class="flex justify-between">
                    <form id="search-user" action="{{ route('SearchReservationInvoices') }}" method="get">
                        <div class="flex w-full">
                            <div class="mr-3">
                                <label for="academic_year"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Academic Year</label>
                                <select id="academic_year" name="academic_year"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="" disabled selected>Select Academic Year...</option>
                                    @foreach($academicYears as $academicYear)
                                        <option
                                            @if(isset($_GET['academic_year']) and $_GET['academic_year']==$academicYear->id) selected
                                            @endif value="{{$academicYear->id}}">{{$academicYear->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    @if(empty($students))
                        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
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
                                    There is not any student informations to show!
                                </div>
                            </div>
                        </div>
                    @else
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4 text-center">
                                        Appliance ID
                                </th>
                                <th scope="col" class="p-4 text-center">
                                        Student ID
                                </th>
                                <th scope="col" class=" text-center">
                                    Academic Year
                                </th>
                                <th scope="col" class=" text-center">
                                    Information
                                </th>
                                <th scope="col" class=" text-center">
                                    Gender
                                </th>
                                <th scope="col" class=" text-center">
                                    Interview Status
                                </th>
                                <th scope="col" class=" text-center">
                                    Document Upload Status
                                </th>
                                <th scope="col" class=" text-center">
                                    Document Approval Status
                                </th>
                                <th scope="col" class=" text-center">
                                    Document Approval Seconder
                                </th>
                                <th scope="col" class=" text-center">
                                    Tuition Payment Status
                                </th>
                                <th scope="col" class=" text-center">
                                    Approval Status
                                </th>
                                <th scope="col" class=" text-center">
                                    Description
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($students as $student)
                                <tr
                                    class="bg-white border dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4 border text-center">
                                            {{ $student->id }}
                                    </td>
                                    <td class="w-4 p-4 border text-center">
                                            {{ $student->student_id }}
                                    </td>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">{{ $student->academicYearInfo->name }}</div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">{{ $student->studentInfo->generalInformationInfo->first_name_en }} {{ $student->studentInfo->generalInformationInfo->last_name_en }}</div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">{{ $student->studentInfo->generalInformationInfo->gender }}</div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">{{ $student->interview_status }}</div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                @switch($student->documents_uploaded)
                                                    @case('0')
                                                        Pending For Upload
                                                        @break
                                                    @case('1')
                                                        Admitted
                                                        @break
                                                    @case('2')
                                                        Pending For Review
                                                        @break
                                                    @case('3')
                                                        Rejected
                                                        @break
                                                    @default
                                                        -
                                                @endswitch
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                @switch($student->documents_uploaded_approval)
                                                    @case(1)
                                                        Approved
                                                        @break
                                                    @case(2)
                                                        Rejected
                                                        @break
                                                    @default
                                                        -
                                                @endswitch
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                @if($student->documentSeconder)
                                                    {{ $student->documentSeconder->generalInformationInfo->first_name_en }} {{ $student->documentSeconder->generalInformationInfo->last_name_en }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                @switch($student->tuition_payment_status)
                                                    @case('Not Paid')
                                                        Not Paid Yet!
                                                        @break
                                                    @case('Paid')
                                                        Paid
                                                        @break
                                                    @case('Pending')
                                                        Pending For Pay
                                                        @break
                                                    @default
                                                        -
                                                @endswitch
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                @switch($student->approval_status)
                                                    @case(1)
                                                        Approved
                                                        @break
                                                    @case(2)
                                                        Rejected
                                                        @break
                                                    @default
                                                        -
                                                @endswitch
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center border text-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="pl-3">
                                            <div
                                                class="text-base font-semibold">
                                                {{$student->description}}
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
        @if(!empty($students))
            <div class="pagination text-center">
                {{ $students->links() }}
            </div>
        @endif
    </div>
@endsection
