<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vulnerability Toggles') }}
        </h2>
    </x-slot>

    @vite(['resources/js/vulnerability_toggles.js'])

    <div id="app-data" data-update-url="{{ route('vulnerability_toggles.update') }}" data-csrf-token="{{ csrf_token() }}">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p>If you want to enable a vulnerable feature, check the corresponding checkbox.</p>
                        <form method="POST" action=data-update-url class="space-y-4">
                            @csrf

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="dark:bg-gray-800 dark:text-gray-400 px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enable/Disable</th>
                                        <th class="dark:bg-gray-800 dark:text-gray-400 px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vulnerability Type</th>
                                        <th class="dark:bg-gray-800 dark:text-gray-400 px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Associated Page</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                                    @php
                                    $vulnerabilities = [
                                    'sqli_on' => ['SQL Injection', 'Change Password'],
                                    'cmd_inject_on' => ['Command Injection', 'Request Medical Records'],
                                    'idor_on' => ['Insecure Direct Object Reference', 'Patient Information & Admin Page'],
                                    'file_upload_on' => ['File Upload', 'Add Medical Records'],
                                    'xss_stored_on' => ['Stored Cross Site Scripting', 'Patient Feedback'],
                                    'xss_reflected_on' => ['Reflected Cross Site Scripting', 'Patient Feedback'],
                                    ];
                                    @endphp

                                    @foreach ($vulnerabilities as $toggle => $details)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    class="toggle-checkbox"
                                                    id="{{ $toggle }}"
                                                    data-toggle="{{ $toggle }}"
                                                    data-vuln-name="{{ $details[0] }}"
                                                    {{ $user[$toggle] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                                            <label for="{{ $toggle }}" class="text-sm text-gray-900 dark:text-white">{{ $details[0] }}</label>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                                            <span class="text-sm text-gray-500 dark:text-white">{{ $details[1] }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div id="notification" class="hidden fixed w-full flex items-center justify-center" style="left: 100px;">
                        <div class="px-4 py-2 rounded-lg shadow-lg text-center font-semibold w-full">
                            <span id="notification-text"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>