<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Feedback') }}
        </h2>
    </x-slot>

    <div class="w-11/12 max-w-7xl mx-auto mt-8 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-800 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-wrap -mx-4">
            <!-- Comment Form -->
            <div class="w-full md:w-1/2 px-4 mb-4">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold">
                        Add Comment
                    </div>
                    <div class="p-4">
                        <form action="{{ route('feedback.store') }}" method="POST">
                            <div class="mb-4">
                                <label for="name" class="block mb-2 font-semibold">Name</label>
                                <input type="text" class="w-full p-2 border border-gray-300 rounded text-base mb-4" id="name" name="name" required>
                            </div>
                            <div class="mb-4">
                                <label for="comment" class="block mb-2 font-semibold">Comment</label>
                                <textarea class="w-full p-2 border border-gray-300 rounded text-base mb-4 min-h-[100px] resize-y" id="comment" name="comment" required></textarea>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out">
                                Submit Comment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="w-full md:w-1/2 px-4 mb-4">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold">
                        Search Comments
                    </div>
                    <div class="p-4">
                        <form action="{{ route('feedback.search') }}" method="GET">
                            <div class="mb-4">
                                <label for="search_name" class="block mb-2 font-semibold">Search by Name</label>
                                <input type="text" class="w-full p-2 border border-gray-300 rounded text-base mb-4" id="search_name" name="search_name" required>
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
            <div class="text-center mt-8 mb-8">
                <h4>Showing results for: {!! $search_name !!}</h4>
            </div>
            <div class="text-center mt-8 mb-8">
                <a href="{{ route('feedback') }}" class="w-full md:w-auto px-6 py-4 bg-gray-500 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-gray-600 hover:shadow-lg focus:bg-gray-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-700 active:shadow-lg transition duration-150 ease-in-out">See All Posts</a>
            </div>
        @endif

        <!-- Comments Display -->
        <div class="bg-white rounded-lg shadow-sm mt-8">
            <div class="px-4 py-4 bg-gray-50 border-b border-gray-200 rounded-t-lg font-semibold">
                Comments
            </div>
            <div class="p-4">
                @if($comments->count() > 0)
                    @foreach($comments as $comment)
                        <div class="border-b border-gray-200 mb-4 pb-4">
                            <h5 class="mb-2 text-lg font-medium">{{ $comment->name }}</h5>
                            {!! $comment->comment !!}
                            <br>
                            <small class="text-gray-600">Posted on: {{ $comment->created_at->format('M d, Y H:i') }}</small>
                        </div>
                    @endforeach
                @else
                    <p>No comments found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>