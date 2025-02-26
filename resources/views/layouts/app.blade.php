<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>BeaverHealth</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-gray-100 dark:bg-gray-900">
            <!-- Sidebar Component -->
            @include('layouts.sidebar')

            <div class="flex-1 ml-64">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
    @if(session('status'))
        <x-status-message
            :message="session('status')['message']"
            :type="session('status')['type']"
        />
    @endif
</html>
