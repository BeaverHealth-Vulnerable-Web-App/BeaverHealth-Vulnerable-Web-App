<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Medical Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="width: 500px !important;">
            <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- File Upload Form -->
                    <form action="{{ route('records.add.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Patient Selection -->
                        <x-patient-dropdown :patients="$patients" />

                        <!-- File input -->
                        <div class="mb-4">
                            <label for="medical_record" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">
                                Select File:
                            </label>
                            <input type="file" name="medical_record" id="medical_record" required
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                            <!-- Validation Error for File -->
                            @error('medical_record')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full px-6 py-4 bg-gray-500 text-white rounded shadow-md hover:bg-gray-600 mt-6">
                            Upload File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages using the component -->
    @if (session('success'))
        <x-status-message :message="session('success')" type="success" />
    @endif

    @if (session('error'))
        <x-status-message :message="session('error')" type="error" />
    @endif
</x-app-layout>
