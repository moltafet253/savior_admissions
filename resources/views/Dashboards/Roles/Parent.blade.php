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
@endif
@if( session()->has('success') )
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
<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="lg:col-span-2 col-span-3 ">
        @can('students-list')
            <div class="bg-white dark:bg-gray-800 dark:text-white p-8 rounded-lg mb-4">
                <div class=" mb-6 md:grid-cols-2">
                    <div class="relative overflow-x-auto">
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <h1 class="text-xl font-semibold text-black dark:text-white ">All Students </h1>

                        </div>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr class="text-center">
                                <th scope="col" class="px-6 py-3 w-5">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Information
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Gender
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Birthdate
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Birthplace
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Show
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">{{$student->student_id}}
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">
                                                {{ $student->generalInformations->first_name_en . " " . $student->generalInformations->last_name_en }}
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">
                                                {{ $student->generalInformations->gender }}
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">
                                                {{ $student->generalInformations->birthdate }}
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 py-3 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">
                                                {{ $student->generalInformations->birthplaceInfo->en_short_name }}
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            @can('students-show')
                                                <a href="{{ route('Students.show',$student->student_id) }}"
                                                   type="button"
                                                   class="min-w-max inline-flex font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300  rounded-lg text-sm px-3 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 hover:underline">
                                                    <div class="text-center">
                                                        <i class="las la-eye "></i>
                                                    </div>
                                                </a>
                                            @endcan
                                        </div>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
        @can('applications-list')
            <div class="bg-white dark:bg-gray-800 dark:text-white p-8 rounded-lg mb-4">
                <div class=" mb-6 md:grid-cols-2">
                    <div class="relative overflow-x-auto">
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <h1 class="text-xl font-semibold text-black dark:text-white ">Application Status </h1>
                        </div>
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">
                                    Academic Year
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Student
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Action
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($applicationStatuses as $applicationStatus)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="w-56 p-4">
                                        {{$applicationStatus->academicYearInfo->name}}
                                    </td>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div
                                                class="text-base font-semibold">{{ $applicationStatus->studentInfo->generalInformationInfo->first_name_en }} {{ $applicationStatus->studentInfo->generalInformationInfo->last_name_en }}</div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            <div class="flex justify-between mb-1">
                                                @php
                                                    $statusPercent=0;
                                                    $statusText='';
                                                    $statusColor='green';
                                                     if ($applicationStatus->interview_status=='Admitted'){
                                                         $statusPercent+=25;
                                                         $statusText='Waiting For Upload Documents';
                                                     }
                                                     if ($applicationStatus->documents_uploaded==1){
                                                         $statusPercent+=25;
                                                         $statusText='Waiting For Documents Approval';
                                                     }
                                                     if ($applicationStatus->documents_uploaded_approval==1){
                                                         $statusPercent+=25;
                                                         $statusText='Waiting For Tuition Payment';
                                                     }
                                                     if ($applicationStatus->documents_uploaded_approval==2){
                                                         $statusPercent-=25;
                                                         $statusColor='red';
                                                     }
                                                     if ($applicationStatus->tuition_payment_status==1){
                                                         $statusPercent+=25;
                                                         $statusText='Tuition Paid. Application Done!';
                                                     }
                                                @endphp
                                                <span
                                                    class="text-base font-medium text-{{$statusColor}}-700 dark:text-white">{{$statusText}}</span>
                                                <span
                                                    class="text-sm font-medium text-{{$statusColor}}-700 dark:text-white">{{$statusPercent}}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                <div class="bg-{{$statusColor}}-600 h-2.5 rounded-full"
                                                     style="width: {{$statusPercent}}%"></div>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="row"
                                        class=" items-center text-center px-6 text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>
                                            @if($applicationStatus->documents_uploaded==0)
                                                <div
                                                    class="text-base font-semibold">
                                                    <a href="{{ route('Document.UploadByParent',$student->student_id) }}"
                                                       type="button"
                                                       class="min-w-max inline-flex font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300  rounded-lg text-sm px-3 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 hover:underline">
                                                        <div class="text-center">
                                                            <i title="Click for upload documents"
                                                               class="las la-cloud-upload-alt "
                                                               style="font-size: 20px"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</div>