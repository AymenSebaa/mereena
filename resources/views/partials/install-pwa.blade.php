<div id="pwa-install-banner"
    class="glass-pwa-banner d-none d-flex justify-content-between align-items-center px-4 py-3 z-50 animate-on-load">
    <div class="text-light">
        <span id="pwa-install-text" class="fw-semibold">Add this app to your home screen</span>
    </div>
    <div class="d-flex gap-2">
        <button id="pwa-install-btn" class="btn btn-sm glass-btn install-btn">
            <i class="bi bi-download me-1"></i>Install
        </button>
        <button id="enable-notifications-btn" class="btn btn-sm glass-btn notif-btn">
            <i class="bi bi-bell me-1"></i>Enable
        </button>
        <button id="pwa-close-btn" class="btn btn-sm glass-btn close-btn">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<style>
    .glass-pwa-banner {
        position: fixed;
        bottom: 30px;
        left: -47.7%;
        transform: translateX(20px);
        background: rgba(255, 255, 255, 0.12) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 16px 20px;
        width: calc(100% - 40px);
        z-index: 1040;
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05);
        margin: 0 auto;
        animation: slideUpIn 0.5s ease-out;
    }

    .glass-btn {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: white !important;
        border-radius: 12px !important;
        padding: 8px 16px !important;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .glass-btn:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .glass-btn:active {
        transform: translateY(0);
    }

    .install-btn:hover {
        background: linear-gradient(135deg, rgba(0, 99, 65, 0.3), rgba(0, 99, 65, 0.5)) !important;
        border-color: rgba(0, 99, 65, 0.4) !important;
    }

    .notif-btn:hover {
        background: linear-gradient(135deg, rgba(255, 165, 0, 0.3), rgba(255, 165, 0, 0.5)) !important;
        border-color: rgba(255, 165, 0, 0.4) !important;
    }

    .close-btn:hover {
        background: linear-gradient(135deg, rgba(255, 0, 0, 0.3), rgba(255, 0, 0, 0.5)) !important;
        border-color: rgba(255, 0, 0, 0.4) !important;
    }

    /* Animations 
    @keyframes slideUpIn {
        from {
            opacity: 0;
            transform: translateX(50%) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(50%) translateY(20px);
        }
    }

    @keyframes slideDownOut {
        to {
            opacity: 0;
            transform: translateX(0%) translateY(-50%);
        }
    }*/

    .glass-pwa-banner.hiding {
        animation: slideDownOut 0.4s ease-in forwards;
    }

    /* Floating animation */
    @keyframes float {
        0%, 100% {
            transform: translateX(50%) translateY(0px);
        }
        50% {
            transform: translateX(50%) translateY(-5px);
        }
    }

    .glass-pwa-banner {
        animation: float 6s ease-in-out infinite;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .glass-pwa-banner {
            width: calc(100% - 30px);
            padding: 14px 18px;
        }
        
        .glass-btn {
            padding: 7px 14px !important;
            font-size: 0.85rem;
        }
        
        #pwa-install-text {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .glass-pwa-banner {
            width: calc(100% - 20px);
            padding: 12px 16px;
            flex-direction: column;
            gap: 12px;
        }
        
        .glass-btn {
            padding: 8px 12px !important;
            font-size: 0.8rem;
        }
        
        #pwa-install-text {
            text-align: center;
            font-size: 0.85rem;
        }
        
        .d-flex.gap-2 {
            width: 100%;
            justify-content: center;
        }
    }

</style>

<script>
    let deferredPrompt;
    const banner = document.getElementById("pwa-install-banner");
    const installBtn = document.getElementById("pwa-install-btn");
    const closeBtn = document.getElementById("pwa-close-btn");
    const notifBtn = document.getElementById("enable-notifications-btn");
    const installText = document.getElementById("pwa-install-text");

    function checkNotificationPermission() {
        if (!("Notification" in window)) {
            notifBtn.classList.add("d-none");
            return;
        }
        if (Notification.permission === "granted" || Notification.permission === "denied") {
            notifBtn.classList.add("d-none");
        }
    }

    function checkIfInstalled() {
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            installBtn.classList.add("d-none");
        }
    }

    function updateBannerVisibility() {
        // hide if nothing to show
        if (installBtn.classList.contains("d-none") && notifBtn.classList.contains("d-none")) {
            banner.classList.add("d-none");
            return;
        }
        // otherwise show
        banner.classList.remove("d-none");
    }

    // --- DOM Ready ---
    document.addEventListener("DOMContentLoaded", () => {
        // small delay avoids flash
        setTimeout(() => {
            checkIfInstalled();
            checkNotificationPermission();
            updateBannerVisibility();
        }, 1000);
    });

    // --- Android beforeinstallprompt ---
    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();
        deferredPrompt = e;
        checkIfInstalled();
        updateBannerVisibility();
    });

    // --- iOS Safari ---
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    const isInStandalone = window.navigator.standalone === true;

    if (isIos && !isInStandalone) {
        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(() => {
                installText.textContent = "Tap Share → Add to Home Screen";
                installBtn.classList.add("d-none");
                updateBannerVisibility();
            }, 1000);
        });
    }

    // --- Install button ---
    installBtn?.addEventListener("click", async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response: ${outcome}`);
            deferredPrompt = null;
            hideBanner();
        }
    });

    // --- Notifications button ---
    notifBtn?.addEventListener("click", async () => {
        if (Notification.permission === "default") {
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                // Create a subtle notification using a toast instead of browser notification
                showToast("🎉 Notifications enabled!");
                notifBtn.classList.add("d-none");
                updateBannerVisibility();
            }
        }
    });

    // --- Close button ---
    closeBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        hideBanner();
    });

    function hideBanner() {
        banner.classList.add("hiding");
        setTimeout(() => {
            banner.classList.add("d-none");
            banner.style.display = "none";
        }, 400);
    }

    function showToast(message) {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = 'glass-toast';
        toast.innerHTML = `
            <div class="glass-toast-content">
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Add toast styles
    const toastStyles = document.createElement('style');
    toastStyles.textContent = `
        .glass-toast {
            position: fixed;
            top: 50px;
            left: 50%;
            transform: translateX(50%) translateY(20px);
            background: rgba(0, 99, 65, 0.9);
            backdrop-filter: blur(20px);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            z-index: 1050;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        .glass-toast.show {
            opacity: 1;
            transform: translateX(20px) translateY(0);
        }
        
        .glass-toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    `;
    document.head.appendChild(toastStyles);

    // --- React to state change ---
    document.addEventListener("visibilitychange", () => {
        checkIfInstalled();
        checkNotificationPermission();
        updateBannerVisibility();
    });
</script>