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

                        <!-- File input with JavaScript validation -->
                        <div class="mb-4">
                            <label for="medical_record" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">
                                Select File:
                            </label>
                            <input type="file" name="medical_record" id="medical_record" required
                                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   onchange="validateFileSize(this)">
                            
                            <!-- File Size Error Message -->
                            <div id="file-size-error" class="text-red-600 text-sm mt-1" style="display: none;"></div>

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

    <!-- Status message -->
    @if (session('records-status'))
        <x-status-message
            :message="session('records-status')['message']"
            :type="session('records-status')['type']"
        />
    @endif

    <!-- JavaScript for File Size Validation -->
    <script>
        function validateFileSize(input) {
            const maxSize = 100 * 1024 * 1024;
            const file = input.files[0];

            if (file && file.size > maxSize) {
                document.getElementById('file-size-error').innerText = 'File size exceeds the maximum limit of 100MB.';
                document.getElementById('file-size-error').style.display = 'block';
                input.value = '';
            } else {
                document.getElementById('file-size-error').style.display = 'none';
            }
        }
    </script>
</x-app-layout>
