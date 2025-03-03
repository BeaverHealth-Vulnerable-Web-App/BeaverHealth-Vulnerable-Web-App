import { showStatusMessage, HTTP_STATUS } from './utils.js';
import { updateSidebar } from './sidebar.js';

/**
 * Handles checkbox change events for role toggles
 * @param {Event} event The change event
 */
async function handleRoleCheckboxChange(event) {
    const checkbox = event.currentTarget;
    const targetUserId = checkbox.dataset.targetUserId;
    const role = checkbox.dataset.role;
    const isChecked = checkbox.checked;
    const originalState = !isChecked;

    if (!hasPermissionToModifyRoles()) {
        revertCheckbox(checkbox, originalState, 'Insufficient permissions');
        return;
    }

    try {
        const result = await updateUserRole(targetUserId, role, isChecked);

        if (result.success) {
            // Only update sidebar if user is changing their own role
            if (targetUserId === window.currentUserId) {
                await updateSidebar();
            }
        } else {
            revertCheckbox(checkbox, originalState, result.message || 'Failed to update permissions');
        }
    } catch (error) {
        console.error('Error:', error);
        revertCheckbox(checkbox, originalState, 'Error updating role');
    }
}

/**
 * Checks if current user has permission to modify roles
 * @returns {boolean} True if user has permission
 */
function hasPermissionToModifyRoles() {
    const isAdmin = window.currentUserIsAdmin === 'true';
    const bacOn = window.currentUserBacOn === 'true';

    return isAdmin || bacOn;
}

/**
 * Sends request to update a user's role
 * @param {string} targetUserId The ID of the user to update
 * @param {string} role The role to update
 * @param {boolean} value The new value for the role
 * @returns {Object} Result with success status and message
 */
async function updateUserRole(targetUserId, role, value) {
    const response = await fetch(window.appRoutes.updateRole, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_id: targetUserId,
            role: role,
            value: value
        })
    });

    if (response.status === HTTP_STATUS.FORBIDDEN) {
        return { success: false, message: 'Insufficient permissions' };
    }

    if (!response.ok) {
        throw new Error(`Server error: ${response.status}`);
    }

    return await response.json();
}

/**
 * Reverts a checkbox to its original state and shows an error message
 * @param {HTMLElement} checkbox The checkbox to revert
 * @param {boolean} state The state to revert to
 * @param {string} message The error message to display
 */
function revertCheckbox(checkbox, state, message) {
    checkbox.checked = state;
    showStatusMessage(message, 'error');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.role-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', handleRoleCheckboxChange);
    });
});
