<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Feedback') }}
        </h2>
    </x-slot>

    <div class="w-11/12 max-w-7xl mx-auto mt-8 px-4 dark:text-white dark:bg-gray-800">
        @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex flex-wrap -mx-4">
            <!-- Comment Form -->
            <div class="w-full md:w-1/2 px-4 mb-4">
                <div class="bg-white rounded-lg shadow-sm dark:text-white dark:bg-gray-800">
                    <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold dark:text-white dark:bg-gray-800">
                        Add Comment
                    </div>
                    <div class="p-4">
                        <form action="{{ route('feedback.store') }}" method="POST">
                            @csrf
                            <x-patient-dropdown :patients="$patients" />
                            <div class="mb-4">
                                <label for="feedback" class="block mb-2 font-semibold">Feedback</label>
                                <textarea class="w-full p-2 border border-gray-300 rounded text-base mb-4 min-h-[100px] resize-y dark:text-white dark:bg-gray-800" id="feedback" name="feedback" required></textarea>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out">
                                Submit Feedback
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="w-full md:w-1/2 px-4 mb-4 dark:text-white dark:bg-gray-800">
                <div class="bg-white rounded-lg shadow-sm dark:text-white dark:bg-gray-800">
                    <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold dark:text-white dark:bg-gray-800">
                        Search Comments
                    </div>
                    <div class="p-4 dark:text-white dark:bg-gray-800">
                        <form action="{{ route('feedback.search') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="search_name" class="block mb-2 font-semibold dark:text-white dark:bg-gray-800">Search by Name</label>
                                <input type="text" class="w-full p-2 border border-gray-300 rounded text-base mb-4 dark:text-white dark:bg-gray-800" id="search_name" name="search_name" required>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out">
                                Search
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($search_name))
        <div class="text-center mt-8 mb-8 dark:text-white dark:bg-gray-800">
            <h4>Showing results for: {!! $search_name !!}</h4>
        </div>
        <div class="text-center mt-8 mb-8 dark:text-white dark:bg-gray-800">
            <a href="{{ route('feedback') }}" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out dark:text-white dark:bg-gray-800">See All Posts</a>
        </div>
        @endif

        <!-- Comments Display -->
        <div class="bg-white rounded-lg shadow-sm mt-8 dark:text-white dark:bg-gray-800">
            <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold dark:text-white dark:bg-gray-800">
                Comments
            </div>
            <div class="p-4">
                @if($feedback->count() > 0)
                @foreach($feedback as $comment)
                <div class="border-b border-gray-200 mb-4 pb-4">
                    <h5 class="mb-2 text-lg font-medium dark:text-white dark:bg-gray-800">
                        @if($comment->patient)
                        {{ $comment->patient->first_name }} {{ $comment->patient->last_name }}
                        @endif
                    </h5>
                    {!! $comment->feedback !!}
                    <br>
                    <small class="text-gray-600 dark:text-white dark:bg-gray-800">Posted on: {{ $comment->created_at->format('M d, Y H:i') }}</small>
                </div>
                @endforeach
                @else
                <p>No comments found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>