export function showStatusMessage(message, type = 'success') {
    const existingMessage = document.getElementById('status-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const messageHTML = `
        <div
            id="status-message"
            class="absolute top-0 right-0 mt-2 mr-2 p-2 rounded text-base ${
                type === 'error' ?
                'bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300' :
                'bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300'
            }"
        >
            ${message}
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', messageHTML);

    const messageEl = document.getElementById('status-message');

    setTimeout(() => {
        messageEl.style.transition = 'opacity 0.5s ease-in-out';
        messageEl.style.opacity = '0';
        setTimeout(() => {
            messageEl.remove();
        }, 500);
    }, 3000);
}
