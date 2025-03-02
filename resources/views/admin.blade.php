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
                    <x-table :headers="[
                        ['text' => 'User'],
                        ['text' => 'Administrator', 'align' => 'center'],
                        ['text' => 'Request Records', 'align' => 'center'],
                        ['text' => 'Add Records', 'align' => 'center'],
                        ['text' => 'View Patient Info', 'align' => 'center']
                    ]">
                        @foreach($users as $user)
                            <x-table-row>
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
                            </x-table-row>
                        @endforeach
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
