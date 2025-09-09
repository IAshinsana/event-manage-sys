// Event Management Confirmations and Actions
class EventManager {
    constructor() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Archive event confirmation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-archive-event')) {
                e.preventDefault();
                this.confirmArchiveEvent(e.target);
            }
        });

        // Reactivate event confirmation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-reactivate-event')) {
                e.preventDefault();
                this.confirmReactivateEvent(e.target);
            }
        });

        // Delete event confirmation (admin only, for events with no tickets sold)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete-event')) {
                e.preventDefault();
                this.confirmDeleteEvent(e.target);
            }
        });
    }

    confirmArchiveEvent(button) {
        const eventId = button.dataset.eventId;
        const eventTitle = button.dataset.eventTitle;
        const hasTicketsSold = button.dataset.hasTickets === 'true';

        const modal = this.createConfirmationModal({
            title: '🗄️ Archive Event',
            message: `Are you sure you want to archive the event "<strong>${eventTitle}</strong>"?`,
            details: hasTicketsSold ?
                '⚠️ <strong>Warning:</strong> This event has tickets sold. Archiving will hide it from public view but preserve all booking data.' :
                'This event will be hidden from public view and moved to archived events.',
            confirmText: 'Yes, Archive Event',
            confirmClass: 'btn-warning',
            cancelText: 'Cancel',
            inputRequired: true,
            inputLabel: 'Reason for archiving (optional):',
            inputPlaceholder: 'e.g., Event postponed, Venue unavailable, etc.',
            onConfirm: (reason) => {
                this.archiveEvent(eventId, reason);
            }
        });

        document.body.appendChild(modal);
    }

    confirmReactivateEvent(button) {
        const eventId = button.dataset.eventId;
        const eventTitle = button.dataset.eventTitle;
        const userRole = button.dataset.userRole;

        const isCoordinator = userRole === 'coordinator';
        const needsApproval = isCoordinator;

        const modal = this.createConfirmationModal({
            title: '🔄 Reactivate Event',
            message: `Are you sure you want to reactivate the event "<strong>${eventTitle}</strong>"?`,
            details: needsApproval ?
                '📋 <strong>Note:</strong> As a coordinator, reactivating this event will send it for admin approval again.' :
                'This event will be made public and available for bookings immediately.',
            confirmText: needsApproval ? 'Send for Approval' : 'Yes, Reactivate',
            confirmClass: 'btn-success',
            cancelText: 'Cancel',
            onConfirm: () => {
                this.reactivateEvent(eventId, needsApproval);
            }
        });

        document.body.appendChild(modal);
    }

    confirmDeleteEvent(button) {
        const eventId = button.dataset.eventId;
        const eventTitle = button.dataset.eventTitle;
        const hasTicketsSold = button.dataset.hasTickets === 'true';

        if (hasTicketsSold) {
            this.showAlert('❌ Cannot Delete Event', 'This event has tickets sold and cannot be deleted. Please archive it instead.', 'error');
            return;
        }

        const modal = this.createConfirmationModal({
            title: '⚠️ Delete Event Permanently',
            message: `Are you <strong>absolutely sure</strong> you want to permanently delete "<strong>${eventTitle}</strong>"?`,
            details: '🚨 <strong>This action cannot be undone!</strong> All event data, ticket types, and related information will be permanently removed.',
            confirmText: 'Yes, Delete Forever',
            confirmClass: 'btn-danger',
            cancelText: 'Cancel',
            inputRequired: true,
            inputLabel: 'Type "DELETE" to confirm:',
            inputPlaceholder: 'DELETE',
            validateInput: (value) => value.toUpperCase() === 'DELETE',
            onConfirm: () => {
                this.deleteEvent(eventId);
            }
        });

        document.body.appendChild(modal);
    }

    createConfirmationModal(options) {
        const modal = document.createElement('div');
        modal.className = 'confirmation-modal-overlay';
        modal.innerHTML = `
            <div class="confirmation-modal">
                <div class="confirmation-header">
                    <h3>${options.title}</h3>
                </div>
                <div class="confirmation-body">
                    <p>${options.message}</p>
                    ${options.details ? `<div class="confirmation-details">${options.details}</div>` : ''}
                    ${options.inputRequired ? `
                        <div class="confirmation-input-group">
                            <label>${options.inputLabel}</label>
                            <input type="text" id="confirmationInput" placeholder="${options.inputPlaceholder}" />
                            <small class="input-error" style="display: none; color: var(--danger);">Invalid input. Please try again.</small>
                        </div>
                    ` : ''}
                </div>
                <div class="confirmation-actions">
                    <button class="btn btn-outline confirmation-cancel">${options.cancelText}</button>
                    <button class="btn ${options.confirmClass} confirmation-confirm">${options.confirmText}</button>
                </div>
            </div>
        `;

        // Handle modal actions
        const cancelBtn = modal.querySelector('.confirmation-cancel');
        const confirmBtn = modal.querySelector('.confirmation-confirm');
        const input = modal.querySelector('#confirmationInput');

        cancelBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
        });

        confirmBtn.addEventListener('click', () => {
            if (options.inputRequired) {
                const value = input.value.trim();
                if (options.validateInput && !options.validateInput(value)) {
                    const errorEl = modal.querySelector('.input-error');
                    errorEl.style.display = 'block';
                    input.focus();
                    return;
                }
                options.onConfirm(value);
            } else {
                options.onConfirm();
            }
            document.body.removeChild(modal);
        });

        // Close on overlay click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });

        // Close on Escape key
        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.body.removeChild(modal);
            }
        });

        return modal;
    }

    async archiveEvent(eventId, reason) {
        try {
            // Determine the correct path based on current page location
            const currentPath = window.location.pathname;
            let actionUrl = 'archive_event.php';
            
            // If we're in admin or coordinator directory, adjust path
            if (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) {
                actionUrl = '../archive_event.php';
            }
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=archive&event_id=${eventId}&reason=${encodeURIComponent(reason)}`
            });

            const result = await response.text();
            if (response.ok) {
                this.showAlert('✅ Event Archived', 'The event has been successfully archived.', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error('Failed to archive event');
            }
        } catch (error) {
            this.showAlert('❌ Error', 'Failed to archive the event. Please try again.', 'error');
        }
    }

    async reactivateEvent(eventId, needsApproval) {

        try {
            // Determine the correct path based on current page location
            const currentPath = window.location.pathname;
            let actionUrl = 'archive_event.php';
            
            // If we're in admin or coordinator directory, adjust path
            if (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) {
                actionUrl = '../archive_event.php';
            }
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=reactivate&event_id=${eventId}&needs_approval=${needsApproval ? '1' : '0'}`
            });

            const result = await response.text();
            if (response.ok) {
                const message = needsApproval ?
                    'Event reactivation request sent for admin approval.' :
                    'The event has been successfully reactivated.';
                this.showAlert('✅ Success', message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error('Failed to reactivate event');
            }
        } catch (error) {
            this.showAlert('❌ Error', 'Failed to reactivate the event. Please try again.', 'error');
        }
    }

    async deleteEvent(eventId) {
        try {
            // Determine the correct path based on current page location
            const currentPath = window.location.pathname;
            let actionUrl = 'archive_event.php';
            
            // If we're in admin or coordinator directory, adjust path
            if (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) {
                actionUrl = '../archive_event.php';
            }
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&event_id=${eventId}`
            });

            const result = await response.text();
            if (response.ok) {
                this.showAlert('✅ Event Deleted', 'The event has been permanently deleted.', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error('Failed to delete event');
            }
        } catch (error) {
            this.showAlert('❌ Error', 'Failed to delete the event. Please try again.', 'error');
        }
    }

    showAlert(title, message, type) {
        const alert = document.createElement('div');
        alert.className = `floating-alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button class="alert-close">&times;</button>
        `;

        document.body.appendChild(alert);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                document.body.removeChild(alert);
            }
        }, 5000);

        // Manual close
        alert.querySelector('.alert-close').addEventListener('click', () => {
            if (alert.parentNode) {
                document.body.removeChild(alert);
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new EventManager();
});
