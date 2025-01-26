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
                            <input type="text" name="first_name" id="first_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div class="mb-4">
                            <label for="last_name" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Last Name:</label>
                            <input type="text" name="last_name" id="last_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div class="mb-4">
                            <label for="dob" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Date of Birth:</label>
                            <input type="date" name="dob" id="dob" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <button type="submit" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out mt-4">
                            Search Records
                        </button>
                    </form>

                    <!-- Database Results -->
                    @if (isset($results))
                        <div class="mt-6">
                            <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Patient Records:</h3>
                            <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="border px-4 py-2 text-gray-800 dark:text-gray-200">First Name</th>
                                        <th class="border px-4 py-2 text-gray-800 dark:text-gray-200">Last Name</th>
                                        <th class="border px-4 py-2 text-gray-800 dark:text-gray-200">Date of Birth</th>
                                        <th class="border px-4 py-2 text-gray-800 dark:text-gray-200">Policy Number</th>
                                        <th class="border px-4 py-2 text-gray-800 dark:text-gray-200">Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $result)
                                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="border px-4 py-2 text-gray-900 dark:text-gray-100">{{ $result->first_name }}</td>
                                            <td class="border px-4 py-2 text-gray-900 dark:text-gray-100">{{ $result->last_name }}</td>
                                            <td class="border px-4 py-2 text-gray-900 dark:text-gray-100">{{ $result->date_of_birth }}</td>
                                            <td class="border px-4 py-2 text-gray-900 dark:text-gray-100">{{ $result->policy_number }}</td>
                                            <td class="border px-4 py-2 text-gray-900 dark:text-gray-100">{{ $result->address }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Log Search Output -->
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded shadow-sm">
                        <h3 class="font-semibold">Medical Files:</h3>
                        <pre class="bg-gray-50 dark:bg-gray-800 p-4 rounded text-gray-800 dark:text-gray-200">{{ $logOutput }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>