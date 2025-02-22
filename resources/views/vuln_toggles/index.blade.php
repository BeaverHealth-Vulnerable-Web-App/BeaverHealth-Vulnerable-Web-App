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
                        <p class="mb-6">If you want to enable a vulnerable feature, check the corresponding checkbox.</p>

                        <table class="table-auto w-full bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg">
                            <thead>
                                <tr class="bg-gray-700 text-white">
                                    <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Enable/Disable</th>
                                    <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Vulnerability Type</th>
                                    <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Associated Page</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                <tr class="bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        <input
                                            type="checkbox"
                                            class="toggle-checkbox cursor-pointer"
                                            id="{{ $toggle }}"
                                            data-toggle="{{ $toggle }}"
                                            data-vuln-name="{{ $details[0] }}"
                                            {{ $user[$toggle] ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-3">{{ $details[0] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $details[1] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="notification" class="hidden absolute top-0 right-0 mt-2 mr-2 p-2 rounded text-sm">
                    <span id="notification-text"></span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
