export async function updateSidebar() {
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
