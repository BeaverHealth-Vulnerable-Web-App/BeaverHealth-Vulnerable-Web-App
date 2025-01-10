<nav class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed flex flex-col">
    <!-- Logo -->
    <div class="mb-8 text-center">
        <a href="{{ route('dashboard') }}" class="block text-lg font-semibold text-white">
            <button class="px-4 py-2 bg-gray-700 text-white rounded">Beaver Healthcare</button>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-grow">
        <nav class="space-y-4">
            <!-- Records Request Link -->
            @if(auth()->user()->request_records)
                <a href="{{ route('records.request') }}" class="block py-2 px-4 rounded hover:bg-gray-600">Request Records</a>
            @endif

            <!-- Records Add Link -->
            @if(auth()->user()->load_records)
                <a href="{{ route('records.add') }}" class="block py-2 px-4 rounded hover:bg-gray-600">Add Records</a>
            @endif

            <!-- Feedback Link -->
            <a href="{{ route('feedback') }}" class="block py-2 px-4 rounded hover:bg-gray-600">Patient Feedback</a>

            <!-- Admin Link -->
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin') }}" class="block py-2 px-4 rounded hover:bg-gray-600">Admin</a>
            @endif

            <!-- Vulnerability Toggles Link -->
            <a href="{{ route('toggles') }}" class="block py-2 px-4 rounded hover:bg-gray-600">Vulnerability Toggles</a>

            <!-- Profile Link -->
            <a href="{{ route('profile.edit') }}" class="block py-2 px-4 bg-gray-700 rounded hover:bg-gray-600">Profile</a>

            <!-- Log Out Form -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-left block py-2 px-4 bg-gray-700 rounded hover:bg-gray-600">
                    Log Out
                </button>
            </form>
        </nav>
    </div>
</nav>
