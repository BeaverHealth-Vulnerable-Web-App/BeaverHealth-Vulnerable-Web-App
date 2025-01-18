<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient Information
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="mb-2"><strong>Name:</strong> {{ $patient->first_name }} {{ $patient->last_name }}</p>
                    <p class="mb-2"><strong>Date of Birth:</strong> {{ $patient->date_of_birth }}</p>
                    <p class="mb-2"><strong>Policy Number:</strong> {{ $patient->policy_number }}</p>
                    <p class="mb-2"><strong>Address:</strong> {{ $patient->address }}</p>
                    <p class="mb-2"><strong>Employee:</strong> {{ $patient->is_employee ? 'Yes' : 'No' }}</p>
                    <p class="mb-2"><strong>SSN:</strong> {{ $patient->ssn }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
