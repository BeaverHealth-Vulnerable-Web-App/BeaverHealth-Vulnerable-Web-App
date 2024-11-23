<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full bg-gray-200 dark:bg-gray-800 border-separate border-spacing-2">
                        <thead>
                            <tr class="bg-gray-700 text-white">
                                <th class="p-4 text-left">User</th>
                                <th class="p-4 text-center">Administrator</th>
                                <th class="p-4 text-center">Request Records</th>
                                <th class="p-4 text-center">Add Records</th>
                                <th class="p-4 text-center">View Employee Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="bg-gray-100 dark:bg-gray-900 hover:bg-gray-300 dark:hover:bg-gray-700">
                                    <td class="p-4">{{ $user->username }}</td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" class="role-checkbox" data-user-id="{{ $user->user_id }}" data-role="is_admin" {{ $user->is_admin ? 'checked' : '' }}>
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" class="role-checkbox" data-user-id="{{ $user->user_id }}" data-role="request_records" {{ $user->request_records ? 'checked' : '' }}>
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" class="role-checkbox" data-user-id="{{ $user->user_id }}" data-role="load_records" {{ $user->load_records ? 'checked' : '' }}>
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" class="role-checkbox" data-user-id="{{ $user->user_id }}" data-role="view_employee_info" {{ $user->view_employee_info ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle AJAX requests -->
    <script>
        document.querySelectorAll('.role-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', function () {
                const userId = this.dataset.userId;
                const role = this.dataset.role;
                const isChecked = this.checked;

                fetch('{{ route('admin.updateRole') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        role: role,
                        value: isChecked
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Role updated successfully');
                    } else {
                        console.error('Failed to update role');
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            });
        });
    </script>
</x-app-layout>
