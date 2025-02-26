<nav class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed flex flex-col">
    <!-- Logo -->
    <div class="mt-3 mb-6">
        <div class="block px-4 text-lg font-semibold">Beaver Healthcare</div>
    </div>

    <!-- Navigation Links -->
    <div class="flex-grow">
        <nav class="space-y-4">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="block py-2 px-4 rounded {{ request()->routeIs('dashboard') ? 'bg-gray-700' : 'hover:bg-gray-600' }} ">
                Dashboard
            </a>

            <!-- Admin -->
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin') }}"
                   class="block py-2 px-4 rounded {{ request()->routeIs('admin') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                    Admin
                </a>
            @endif

            <!-- Add Medical Records -->
            @if(auth()->user()->load_records)
                <a href="{{ route('records.add') }}"
                   class="block py-2 px-4 rounded {{ request()->routeIs('records.add') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                    Add Medical Records
                </a>
            @endif

            <!-- Request Medical Records -->
            @if(auth()->user()->request_records)
                <a href="{{ route('records.request') }}"
                   class="block py-2 px-4 rounded {{ request()->routeIs('records.request') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                    Request Medical Records
                </a>
            @endif

            <!-- Feedback -->
            <a href="{{ route('feedback') }}"
               class="block py-2 px-4 rounded {{ request()->routeIs('feedback') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                Patient Feedback
            </a>

            <!-- Patient Information -->
            @if(auth()->user()->view_patient_info)
                <a href="{{ route('patients.index') }}"
                   class="block py-2 px-4 rounded {{ request()->routeIs('patients.index') || request()->routeIs('patients.info') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                    Patient Information
                </a>
            @endif

            <!-- Vulnerability Toggles -->
            <a href="{{ route('vulnerability_toggles') }}"
               class="block py-2 px-4 rounded {{ request()->routeIs('vulnerability_toggles') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                Vulnerability Toggles
            </a>

            <!-- Change Password -->
            <a href="{{ route('profile.change-password') }}"
               class="block py-2 px-4 rounded {{ request()->routeIs('profile.change-password') ? 'bg-gray-700' : 'hover:bg-gray-600' }}">
                Change Password
            </a>

            <!-- Log Out -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-left block py-2 px-4 rounded hover:bg-gray-600">
                    Log Out
                </button>
            </form>

        </nav>
    </div>
</nav>
