<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reset Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Warning!</strong>
                        <span class="block sm:inline">You are about to reset the entire application.</span>
                    </div>

                    <p class="mb-4">This action will:</p>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Delete all patient records</li>
                        <li>Wipe the entire database</li>
                        <li>Recreate tables and seed with default data</li>
                        <li>Log you out of the application</li>
                    </ul>

                    <p class="mb-6 font-bold">This action cannot be undone!</p>

                    <div class="flex justify-between">
                        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>

                        <form method="POST" action="{{ route('app.reset') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                                Yes, Reset Everything
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
