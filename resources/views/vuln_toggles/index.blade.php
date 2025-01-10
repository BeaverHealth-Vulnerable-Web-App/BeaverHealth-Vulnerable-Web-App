<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vulnerability Toggles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-6">Beaver HealthCare</h1>

                    <h2 class="text-xl font-semibold mb-4">Vulnerability Toggles</h2>

                    <form method="POST" action="{{ route('vulnerability_toggles.store') }}" class="space-y-4">
                        @csrf

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="sql_injection" id="sql_injection" class="w-5 h-5 rounded border-gray-300" {{ $user->sqli_on ? 'checked' : '' }}>
                                <label for="sql_injection" class="ml-3">SQL Injection</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="command_injection" id="command_injection" class="w-5 h-5 rounded border-gray-300" {{ $user->cmd_inject_on ? 'checked' : '' }}>
                                <label for="command_injection" class="ml-3">Command Injection</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="idor" id="idor" class="w-5 h-5 rounded border-gray-300" {{ $user->idor_on ? 'checked' : '' }}>
                                <label for="idor" class="ml-3">InDirect Object Reference</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="file_upload" id="file_upload" class="w-5 h-5 rounded border-gray-300" {{ $user->file_upload_on ? 'checked' : '' }}>
                                <label for="file_upload" class="ml-3">File Upload</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="stored_xss" id="stored_xss" class="w-5 h-5 rounded border-gray-300" {{ $user->xss_stored_on ? 'checked' : '' }}>
                                <label for="stored_xss" class="ml-3">Stored Cross Site Scripting</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="reflected_xss" id="reflected_xss" class="w-5 h-5 rounded border-gray-300" {{ $user->xss_reflected_on ? 'checked' : '' }}>
                                <label for="reflected_xss" class="ml-3">Reflected Cross Site Scripting</label>
                            </div>

                            {{-- <div class="flex items-center">
                                <input type="checkbox" name="broken_access" id="broken_access" class="w-5 h-5 rounded border-gray-300" {{ $user-> ? 'checked' : '' }}>
                                <label for="broken_access" class="ml-3">Broken Access Controls</label>
                            </div> --}}
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>