<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient Information
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">Name:</strong> {{ $patient->first_name }} {{ $patient->last_name }}
                    </p>
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">Date of Birth:</strong> {{ $patient->date_of_birth }}
                    </p>
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">Policy Number:</strong> {{ $patient->policy_number }}
                    </p>
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">Address:</strong> {{ $patient->address }}
                    </p>
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">Employee:</strong> {{ $patient->is_employee ? 'Yes' : 'No' }}
                    </p>
                    <p class="mb-2 text-gray-800 dark:text-gray-300">
                        <strong class="text-gray-700 dark:text-gray-200">SSN:</strong> {{ $patient->ssn }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
