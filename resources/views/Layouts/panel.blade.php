<!DOCTYPE html>
@php
    use App\Models\User;
    $myInfo=User::find(session('id'));
@endphp
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="page-title"></title>
</head>

<body class="bg-gray-200 dark:bg-gray-900 ">

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start space-x-0 sm:space-x-5">
                <button class="pl-1 sm:inline-block hidden " id="toggleButton">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="25px" height="25px"
                         viewBox="0 0 28 28" fill="none">
                        <path
                            d="M4 7C4 6.44771 4.44772 6 5 6H24C24.5523 6 25 6.44771 25 7C25 7.55229 24.5523 8 24 8H5C4.44772 8 4 7.55229 4 7Z"
                            fill="#9CA3AF"/>
                        <path
                            d="M4 13.9998C4 13.4475 4.44772 12.9997 5 12.9997L16 13C16.5523 13 17 13.4477 17 14C17 14.5523 16.5523 15 16 15L5 14.9998C4.44772 14.9998 4 14.552 4 13.9998Z"
                            fill="#9CA3AF"/>
                        <path
                            d="M5 19.9998C4.44772 19.9998 4 20.4475 4 20.9998C4 21.552 4.44772 21.9997 5 21.9997H22C22.5523 21.9997 23 21.552 23 20.9998C23 20.4475 22.5523 19.9998 22 19.9998H5Z"
                            fill="#9CA3AF"/>
                    </svg>
                </button>

                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="pr-5 inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                              d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>

                <a href="" class="flex ml-2 md:mr-24">
                    <div class="h-14 w-24 mr-3 mainLogo"></div>
                    <span
                        class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Savior School</span>
                </a>
            </div>
            <div class="flex items-center">

                <div class="flex items-center">
                    <button data-tooltip-target="tooltip-bottom" data-tooltip-placement="bottom" type="button"
                            id="theme-toggle"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <div id="tooltip-bottom" role="tooltip"
                             class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Toggle dark mode
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                </div>
                <div class="flex items-center ml-3">
                    <div>
                        <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <div class="w-8 h-8 mr-3 defaultUserIcon"></div>
                        </button>
                    </div>
                    <div
                        class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="dropdown-user">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">
                                {{ $myInfo->name }} {{ $myInfo->family }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                {{ $myInfo->email }}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="/"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                   role="menuitem">Dashboard</a>
                            </li>
                            <li>
                                <a href="/Profile"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                   role="menuitem">Settings</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                   role="menuitem">Sign out</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar"
       class="fixed top-0 left-0 z-40 md:w-[3.6rem] transition-width transition-all duration-300  h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
       aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 overflow-hidden">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="/"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">


                    <svg
                        class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ml-4">Dashboard</span>
                </a>
            </li>

            @if($myInfo->hasRole('SuperAdmin') or $myInfo->hasRole('SchoolAdmin'))
                @if($myInfo->hasRole('SuperAdmin'))
                    <li>
                        <a href="/roles"
                           class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg
                                class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/2000/svg" version="1.0" width="24.000000pt"
                                height="24.000000pt" viewBox="0 0 22 21" preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,24.000000) scale(0.100000,-0.100000)"
                                   fill="currentColor" stroke="none">
                                    <path
                                        d="M106 202 c-7 -11 4 -52 14 -52 10 0 21 41 14 52 -3 4 -9 8 -14 8 -5 0 -11 -4 -14 -8z"/>
                                    <path d="M25 160 c-9 -15 2 -27 30 -32 34 -7 38 1 11 23 -28 22 -32 23 -41 9z"/>
                                    <path d="M173 151 c-12 -10 -20 -21 -17 -24 10 -9 59 9 59 23 0 21 -18 21 -42 1z"/>
                                    <path d="M104 119 c-10 -17 13 -36 27 -22 12 12 4 33 -11 33 -5 0 -12 -5 -16 -11z"/>
                                    <path d="M55 92 c-29 -6 -41 -20 -28 -33 9 -9 18 -6 38 9 27 22 23 31 -10 24z"/>
                                    <path
                                        d="M156 92 c-3 -2 6 -14 20 -25 18 -13 29 -16 36 -9 6 6 8 15 4 20 -6 11 -53 22 -60 14z"/>
                                    <path d="M87 64 c-19 -20 -5 -34 34 -34 33 0 40 3 37 18 -3 17 -57 30 -71 16z"/>
                                </g>
                            </svg>
                            <span class="ml-4">Roles</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="/users"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg
                            class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                        </svg>
                        <span class="ml-4">Users</span>
                    </a>
                </li>
                @if($myInfo->hasRole('SuperAdmin'))
                    <li>
                        <button type="button"
                                class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                            <svg
                                class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 18">
                                <path
                                    d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                            </svg>
                            <span class="flex-1 ml-4 text-left whitespace-nowrap">Catalogs</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-example" class="hidden py-2 space-y-2">
                            <li>
                                <a href="/Schools"
                                   class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    <span class="menulist">Schools</span>
                                </a>
                            </li>
                            <li>
                                <a href="/DocumentTypes"
                                   class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    <span class="menulist">Document types</span>
                                </a>
                            </li>
                            <li>
                                <a href="/EducationYears"
                                   class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    <span class="menulist">Education years</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endif
            <li>
                <a href="/Documents"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg
                        class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ml-4">My Documents</span>
                </a>
            </li>
            <li>
                <a href="/Profile"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg
                        class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ml-4">Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg
                        class=" flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="4 0 19 20"
                        fill="none">
                        <path
                            d="M12 18.25C11.5858 18.25 11.25 18.5858 11.25 19C11.25 19.4142 11.5858 19.75 12 19.75H18C18.9665 19.75 19.75 18.9665 19.75 18V6C19.75 5.0335 18.9665 4.25 18 4.25H12C11.5858 4.25 11.25 4.58579 11.25 5C11.25 5.41421 11.5858 5.75 12 5.75L18 5.75C18.1381 5.75 18.25 5.86193 18.25 6L18.25 18C18.25 18.1381 18.1381 18.25 18 18.25H12Z"
                            fill="currentColor"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M14.5031 14.3652C15.1934 14.3652 15.7531 13.8056 15.7531 13.1152V10.8747C15.7531 10.1843 15.1934 9.6247 14.5031 9.6247L9.89048 9.6247C9.88396 9.55128 9.87713 9.47787 9.87 9.40448L9.81597 8.8486C9.73354 8.00049 8.83294 7.49258 8.06451 7.86084C6.43029 8.64403 4.95085 9.71578 3.69736 11.0245L3.59816 11.1281C3.13395 11.6128 3.13395 12.3771 3.59815 12.8618L3.69736 12.9654C4.95085 14.2741 6.43029 15.3459 8.06451 16.1291C8.83293 16.4973 9.73354 15.9894 9.81597 15.1413L9.87 14.5854C9.87713 14.512 9.88396 14.4386 9.89048 14.3652H14.5031ZM9.19511 12.8652C8.92874 12.8652 8.69326 13.0045 8.56008 13.216C8.49523 13.319 8.45464 13.4391 8.44656 13.5685C8.42842 13.8594 8.40524 14.15 8.37703 14.4403L8.36135 14.6017C7.3253 14.0677 6.36316 13.4028 5.49838 12.6239C5.27402 12.4218 5.05622 12.2121 4.84538 11.995C5.86892 10.9409 7.05651 10.0607 8.36135 9.38824L8.37703 9.54959C8.40524 9.83987 8.42842 10.1305 8.44656 10.4214C8.47122 10.8167 8.79902 11.1247 9.19511 11.1247H14.2531V12.8652H9.19511Z"
                              fill="currentColor"/>
                    </svg>
                    <span class="ml-4 ">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

@yield('content')

</body>
</html>
