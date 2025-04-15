<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Information') }}
        </h2>
    </x-slot>

    @if($errors->has('search'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
                {{ $errors->first('search') }}
            </div>
        </div>
    @endif

    <div class="py-12">
        <!-- Search Form Container (fixed width) -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="width: 500px !important;">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('patients.index') }}" class="mb-4">
                        <input type="text" name="search" placeholder="Enter patient name to search..."
                               value="{{ old('search') }}"
                               class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <button type="submit"
                                class="w-full mt-4 px-6 py-2 bg-gray-500 text-white rounded shadow-md hover:bg-gray-600">
                            Search Patients
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Patient Table: Only display if a search was performed -->
        @if(isset($searchPerformed) && $searchPerformed)
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
                @if(count($patients) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <x-table :headers="[
                            ['text' => 'Name'],
                            ['text' => 'Date of Birth'],
                            ['text' => 'Policy Number'],
                            ['text' => 'Address'],
                            ['text' => 'Details']
                        ]">
                            @foreach ($patients as $patient)
                                <x-table-row>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $patient->date_of_birth }}</td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $patient->policy_number }}</td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $patient->address }}</td>
                                    <td class="px-4 py-3 text-center text-gray-800 dark:text-gray-200">
                                        <a href="{{ route('patients.info', ['id' => $patient->patient_id]) }}"
                                           class="text-blue-500 dark:text-blue-400 hover:underline">
                                            View Details
                                        </a>
                                    </td>
                                </x-table-row>
                            @endforeach
                        </x-table>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-gray-800 dark:text-gray-200">No patients found. Please adjust your search criteria.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
