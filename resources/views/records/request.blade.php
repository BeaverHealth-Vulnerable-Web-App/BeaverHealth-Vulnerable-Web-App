<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Record Lookup') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="width: 500px !important;">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Search Form -->
                    <form action="{{ route('records.search') }}" method="POST">
                        @csrf
                        
                        <!-- Patient Dropdown -->
                        <x-patient-dropdown :patients="$patients" />

                        <div class="mb-4">
                            <label for="keyword" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">
                                Filter Keyword (Optional):
                            </label>
                            <input type="text" name="keyword" id="keyword" 
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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

                        <button type="submit" 
                                class="w-full px-6 py-4 bg-gray-500 text-white rounded shadow-md hover:bg-gray-600 mt-6">
                            Search Records
                        </button>
                    </form>

                    <!-- Patient Details (if found) -->
                    @if (!empty($patientInfo) && $patientInfo[0]->first_name !== 'N/A')
                        <div class="mt-6">
                            <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Patient Details:</h3>
                            <p class="text-gray-900 dark:text-gray-100">
                                <strong>Name:</strong> {{ $patientInfo[0]->first_name }} {{ $patientInfo[0]->last_name }}<br>
                                <strong>DOB:</strong> {{ $patientInfo[0]->date_of_birth }}
                            </p>
                        </div>
                    @endif

                    <!-- Display Matching Files -->
                    @if (session()->has('patient_info') && session()->has('patient_files'))
                        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded shadow-sm">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Matching Files:</h3>
                            
                            @if (!empty(trim($patientFiles)) && trim($patientFiles) !== 'No files found.')
                                {!! $patientFiles !!}
                            @else
                                <p class="text-gray-600 dark:text-gray-300">No files found.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>