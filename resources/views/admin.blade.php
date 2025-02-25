<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <script>
    window.appRoutes = {
        updateRole: '{{ route('admin.updateRole') }}',
        sidebarRefresh: '{{ route('sidebar.refresh') }}'
    };
    window.currentUserId = '{{ auth()->id() }}';
    </script>
    @vite(['resources/js/admin.js'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead>
                            <tr class="bg-gray-700 text-white">
                                <th class="px-4 py-3 text-left border-b border-gray-300 dark:border-gray-700">User</th>
                                <th class="px-4 py-3 text-center border-b border-gray-300 dark:border-gray-700">Administrator</th>
                                <th class="px-4 py-3 text-center border-b border-gray-300 dark:border-gray-700">Request Records</th>
                                <th class="px-4 py-3 text-center border-b border-gray-300 dark:border-gray-700">Add Records</th>
                                <th class="px-4 py-3 text-center border-b border-gray-300 dark:border-gray-700">View Patient Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $user->username }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" class="role-checkbox cursor-pointer" data-user-id="{{ $user->user_id }}" data-role="is_admin" {{ $user->is_admin ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" class="role-checkbox cursor-pointer" data-user-id="{{ $user->user_id }}" data-role="request_records" {{ $user->request_records ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" class="role-checkbox cursor-pointer" data-user-id="{{ $user->user_id }}" data-role="load_records" {{ $user->load_records ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" class="role-checkbox cursor-pointer" data-user-id="{{ $user->user_id }}" data-role="view_patient_info" {{ $user->view_patient_info ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
