<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- File upload form -->
                    <form action="{{ route('records.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Choose a file (PDF, TXT, or CSV):</label>
                            <input type="file" name="file" id="file" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <!-- Display success message -->
                        @if (session('success'))
                            <div class="mt-4 p-2 text-green-800 bg-green-100 border border-green-200 rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        <button type="submit" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out mt-4">
                            Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
