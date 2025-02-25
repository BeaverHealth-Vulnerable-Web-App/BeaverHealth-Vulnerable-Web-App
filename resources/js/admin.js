document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.role-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            const userId = this.dataset.userId;
            const role = this.dataset.role;
            const isChecked = this.checked;

            fetch(window.appRoutes.updateRole, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    user_id: userId,
                    role: role,
                    value: isChecked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Role updated successfully');

                    if (userId === window.currentUserId) {
                        updateSidebar();
                    }
                } else {
                    console.error('Failed to update role');
                }
            })
            .catch(error => {
                console.error('There was a problem:', error);
            });
        });
    });

    function updateSidebar() {
        fetch(window.appRoutes.sidebarRefresh, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector('nav.bg-gray-800').outerHTML = html;
        })
        .catch(error => {
            console.error('Failed to update sidebar:', error);
        });
    }
});
