<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Record Lookup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Search Form -->
                    <form action="{{ route('records.search') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="first_name" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">First Name:</label>
                            <input type="text" name="first_name" id="first_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="last_name" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Last Name:</label>
                            <input type="text" name="last_name" id="last_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="dob" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Date of Birth:</label>
                            <input type="date" name="dob" id="dob" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="keyword" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Filter Keyword (Optional):</label>
                            <input type="text" name="keyword" id="keyword" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        @if (session('success'))
                            <div class="mt-4 p-2 text-green-800 bg-green-100 border border-green-500 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mt-4 p-2 text-red-800 bg-red-100 border border-red-500 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <button type="submit" class="w-full px-6 py-4 bg-gray-500 text-white rounded shadow-md hover:bg-gray-600 mt-6">
                            Search Records
                        </button>
                    </form>

                    <!-- Patient Details (if found) -->
                    @if (!empty($patientInfo) && $patientInfo[0]->first_name !== 'N/A' && $patientInfo[0]->last_name !== 'N/A')
                        <div class="mt-6">
                            <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Patient Details:</h3>
                            <p class="text-gray-900 dark:text-gray-100">
                                <strong>Name:</strong> {{ $patientInfo[0]->first_name }} {{ $patientInfo[0]->last_name }}<br>
                                <strong>DOB:</strong> {{ $patientInfo[0]->date_of_birth }}
                            </p>
                        </div>
                    @endif

                    <!-- Display Matching Files -->
                    @if (!empty(trim($patientFiles)) && trim($patientFiles) !== 'No files found.' && trim($patientFiles) !== 'No files found. (Directory missing)')
                        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded shadow-sm">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Matching Files:</h3>
                            {!! $patientFiles !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>