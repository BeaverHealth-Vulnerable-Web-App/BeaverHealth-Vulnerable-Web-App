<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload Patient Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- File Upload Form -->
                    <form action="{{ route('records.add.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="patient_first_name" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">First Name:</label>
                            <input type="text" name="patient_first_name" id="patient_first_name" required 
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="patient_last_name" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Last Name:</label>
                            <input type="text" name="patient_last_name" id="patient_last_name" required 
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="patient_dob" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Date of Birth:</label>
                            <input type="date" name="patient_dob" id="patient_dob" required 
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="medical_record" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Select File:</label>
                            <input type="file" name="medical_record" id="medical_record" required 
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                            Upload File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>