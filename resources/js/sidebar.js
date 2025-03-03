/**
 * Updates the sidebar with fresh content and highlights the current page
 * @returns {Promise<void>}
 */
export async function updateSidebar() {
    try {
        const html = await fetchSidebarContent();
        replaceSidebar(html);
        highlightCurrentPage();
    } catch (error) {
        console.error('Failed to update sidebar:', error);
    }
}

/**
 * Fetches the updated sidebar content from the server
 * @returns {Promise<string>} The HTML content for the sidebar
 */
async function fetchSidebarContent() {
    const response = await fetch(window.appRoutes.sidebarRefresh, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!response.ok) {
        throw new Error(`Server responded with ${response.status}`);
    }

    return await response.text();
}

/**
 * Replaces the current sidebar with new HTML content
 * @param {string} html The new sidebar HTML
 */
function replaceSidebar(html) {
    const sidebarContainer = document.querySelector('nav.bg-gray-800');
    if (!sidebarContainer) {
        throw new Error('Sidebar container not found');
    }
    sidebarContainer.outerHTML = html;
}

/**
 * Highlights the current page in the sidebar
 */
function highlightCurrentPage() {
    const currentPath = window.location.pathname;
    const currentRoute = currentPath.split('/').pop() || 'dashboard';

    const newSidebar = document.querySelector('nav.bg-gray-800');
    if (!newSidebar) {
        throw new Error('New sidebar not found after replacement');
    }

    const links = newSidebar.querySelectorAll('a');
    links.forEach(link => {
        const href = link.getAttribute('href');
        const isCurrentPage = href && (
            href.includes(currentRoute) ||
            (currentRoute === 'admin' && href.includes('admin'))
        );

        if (isCurrentPage) {
            link.classList.add('bg-gray-700');
            link.classList.remove('hover:bg-gray-600');
        }
    });
}
