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
                        <x-table :headers="[
                            ['text' => 'Enable/Disable'],
                            ['text' => 'Vulnerability Type'],
                            ['text' => 'Associated Page']
                        ]">
                            @php
                            $vulnerabilities = [
                                'sqli_on' => ['SQL Injection', 'Change Password'],
                                'cmd_inject_on' => ['Command Injection', 'Request Medical Records'],
                                'bac_on' => ['Broken Access Control', 'Admin, Patient Info, Add Records, and Request Records'],
                                'file_upload_on' => ['File Upload', 'Add Medical Records'],
                                'xss_stored_on' => ['Stored Cross Site Scripting', 'Patient Feedback'],
                                'xss_reflected_on' => ['Reflected Cross Site Scripting', 'Patient Feedback'],
                            ];
                            @endphp
                            @foreach ($vulnerabilities as $toggle => $details)
                                <x-table-row>
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
                                </x-table-row>
                            @endforeach
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
