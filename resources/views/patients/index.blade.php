<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient List
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Date of Birth</th>
                                <th class="px-4 py-2">Policy Number</th>
                                <th class="px-4 py-2">Address</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <td class="border px-4 py-2">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    <td class="border px-4 py-2">{{ $patient->date_of_birth }}</td>
                                    <td class="border px-4 py-2">{{ $patient->policy_number }}</td>
                                    <td class="border px-4 py-2">{{ $patient->address }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('patients.info', ['id' => $patient->patient_id]) }}" class="text-blue-500 hover:underline">
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
