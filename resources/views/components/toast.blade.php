<!-- Toast Container -->
<div id="toast-container" role="region" aria-label="Notifications" aria-live="polite"></div>

<style>
    /* Toast notification styles */
    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
    }

    .toast {
        background: white;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideIn 0.3s ease-out;
        border-left: 4px solid #3b82f6;
    }

    .toast.success { border-left-color: #10b981; }
    .toast.error { border-left-color: #ef4444; }
    .toast.warning { border-left-color: #f59e0b; }
    .toast.info { border-left-color: #3b82f6; }

    .toast-icon {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
    }

    .toast-icon.success { color: #10b981; }
    .toast-icon.error { color: #ef4444; }
    .toast-icon.warning { color: #f59e0b; }
    .toast-icon.info { color: #3b82f6; }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .toast-message {
        font-size: 13px;
        color: #666;
    }

    .toast-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: #999;
        flex-shrink: 0;
    }

    .toast-close:hover {
        color: #333;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .toast.removing {
        animation: slideOut 0.3s ease-out forwards;
    }
</style>

<script>
    // Toast notification function
    window.showToast = function(title, message, type = 'info', duration = 5000) {
        const container = document.getElementById('toast-container');
        
        // Icon mapping
        const icons = {
            success: 'check_circle',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.setAttribute('role', 'alert');
        
        toast.innerHTML = `
            <span class="material-symbols-outlined toast-icon ${type}">${icons[type] || icons.info}</span>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Close notification">
                <span class="material-symbols-outlined">close</span>
            </button>
        `;

        // Close button handler
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => removeToast(toast));

        // Add to container
        container.appendChild(toast);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => removeToast(toast), duration);
        }

        return toast;
    };

    function removeToast(toast) {
        toast.classList.add('removing');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
</script>
