<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient List
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead>
                            <tr class="bg-gray-700 text-white">
                                <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Name</th>
                                <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Date of Birth</th>
                                <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Policy Number</th>
                                <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">Address</th>
                                <th class="px-4 py-3 text-center border-b border-gray-300 dark:border-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr class="bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    <td class="px-4 py-3">{{ $patient->date_of_birth }}</td>
                                    <td class="px-4 py-3">{{ $patient->policy_number }}</td>
                                    <td class="px-4 py-3">{{ $patient->address }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('patients.info', ['id' => $patient->patient_id]) }}"
                                           class="text-blue-500 dark:text-blue-400 hover:underline">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
