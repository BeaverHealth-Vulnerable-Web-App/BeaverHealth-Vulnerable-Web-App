document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.role-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', async function () {
            const userId = this.dataset.userId;
            const role = this.dataset.role;
            const isChecked = this.checked;

            try {
                const response = await fetch(window.appRoutes.updateRole, {
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
                });

                const data = await response.json();

                if (data.success) {
                    if (userId === window.currentUserId) {
                        await updateSidebar();
                    }
                } else {
                    console.error('Failed to update role');
                }
            } catch (error) {
                console.error('There was a problem:', error);
            }
        });
    });

    async function updateSidebar() {
        try {
            const currentPath = window.location.pathname;
            const currentRoute = currentPath.split('/').pop() || 'dashboard';

            const response = await fetch(window.appRoutes.sidebarRefresh, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const html = await response.text();
            const sidebarContainer = document.querySelector('nav.bg-gray-800');
            sidebarContainer.outerHTML = html;

            const newSidebar = document.querySelector('nav.bg-gray-800');
            const links = newSidebar.querySelectorAll('a');

            links.forEach(link => {
                const href = link.getAttribute('href');
                if (href && (href.includes(currentRoute) ||
                    (currentRoute === 'admin' && href.includes('admin')))) {
                    link.classList.add('bg-gray-700');
                    link.classList.remove('hover:bg-gray-600');
                }
            });
        } catch (error) {
            console.error('Failed to update sidebar:', error);
        }
    }
});
