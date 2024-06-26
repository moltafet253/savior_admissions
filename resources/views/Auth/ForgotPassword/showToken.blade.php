<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css',  'resources/js/login.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password Link</title>
</head>
<body class=" bg-light-theme-color-base dark:bg-gray-800 flex items-center justify-center min-h-screen">
<div
    class="mx-5 sm:mx-0 w-full max-w-2xl p-8 text-center border-gray-200 rounded-lg shadow sm:p-6 md:p-16 dark:bg-gray-800 dark:border-gray-700">
    <form class="space-y-6 " id="forget-password">
        <div class="space-y-2">
            <p class="font-normal text-gray-900 dark:text-gray-400 mb-4">
                Your reset password authentication code: {{ $token }}
            </p>
            <p>
                Please don't share this to anyone!
            </p>
            <p>
                Savior Schools Support
            </p>
        </div>
    </form>
</div>
</body>
</html>
