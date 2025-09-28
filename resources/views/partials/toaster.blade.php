<div id="custom-toast-container"
    style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    gap: 10px;
">
</div>

<script>
    function showToast(message, type = 'info', duration = 3000) {
        const container = document.getElementById('custom-toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.classList.add('custom-toast', type);
        toast.innerText = message;
        container.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);

        // Remove after duration
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, duration);
    }
</script>


<style>
    .custom-toast {
        min-width: 200px;
        padding: 12px 20px;
        border-radius: 6px;
        color: #fff;
        font-weight: 500;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.4s ease;
        pointer-events: auto;
    }

    .custom-toast.show {
        opacity: 1;
        transform: translateX(0);
    }

    .custom-toast.success {
        background-color: #28a745;
    }

    .custom-toast.error {
        background-color: #dc3545;
    }

    .custom-toast.info {
        background-color: #17a2b8;
    }

    .custom-toast.warning {
        background-color: #ffc107;
        color: #212529;
    }
</style>