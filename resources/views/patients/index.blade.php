<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient List
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <table class="table-auto w-full border border-gray-200 dark:border-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-gray-700 dark:text-gray-300 border dark:border-gray-600">Name</th>
                                <th class="px-4 py-2 text-gray-700 dark:text-gray-300 border dark:border-gray-600">Date of Birth</th>
                                <th class="px-4 py-2 text-gray-700 dark:text-gray-300 border dark:border-gray-600">Policy Number</th>
                                <th class="px-4 py-2 text-gray-700 dark:text-gray-300 border dark:border-gray-600">Address</th>
                                <th class="px-4 py-2 text-gray-700 dark:text-gray-300 border dark:border-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr class="bg-white dark:bg-gray-900 even:bg-gray-50 dark:even:bg-gray-800">
                                    <td class="border px-4 py-2 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </td>
                                    <td class="border px-4 py-2 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700">
                                        {{ $patient->date_of_birth }}
                                    </td>
                                    <td class="border px-4 py-2 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700">
                                        {{ $patient->policy_number }}
                                    </td>
                                    <td class="border px-4 py-2 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700">
                                        {{ $patient->address }}
                                    </td>
                                    <td class="border px-4 py-2 border-gray-200 dark:border-gray-700">
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
