<x-guest-layout>
    <div class="w-100" style="max-width:420px;margin:auto;text-align:center;">
        <h2 class="fw-bold mb-3">OTP Verification</h2>
        <p>Enter the 6-digit code sent to <strong>{{ auth()->user()->email }}</strong></p>
        <p class="small mb-4"> <span id="otp-timer">--:--</span></p>

        <div id="notification" class="notification hidden">
            <span id="notification-message"></span>
            <button id="notification-close" class="btn-close btn-close-white"></button>
        </div>

        <form method="POST" action="{{ oRoute('otp.verify.submit') }}" id="otpForm">
            @csrf
            <div class="d-flex justify-content-center gap-2 mb-4">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" class="form-control text-center otp-input" maxlength="1">
                @endfor
            </div>
            <input type="hidden" name="otp_code" id="otp_code">
            <button type="button" id="resendBtn" class="btn btn-link text-primary">Resend OTP</button>
        </form>
        
        <button type="button" class="btn btn-sm mt-4" onclick="logoutAndRefresh()"> ← Back</button>

    </div>

    <script>
        function logoutAndRefresh() {
            fetch("{{ oRoute('logout') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }).finally(() => location.reload());
        }

        const inputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp_code');
        const otpForm = document.getElementById('otpForm');
        const timerEl = document.getElementById('otp-timer');
        const resendBtn = document.getElementById('resendBtn');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        const notificationClose = document.getElementById('notification-close');

        let otpRemaining = 0;
        let resendRemaining = 0;

        function updateHiddenInput() {
            otpHidden.value = Array.from(inputs).map(i => i.value).join('');
        }

        function checkAndSubmit() {
            updateHiddenInput();
            if (otpHidden.value.length === inputs.length) otpForm.submit();
        }

        function showNotification(msg, type = 'success') {
            notificationMessage.textContent = msg;
            notification.className = `notification ${type} show`;
            setTimeout(() => notification.classList.remove('show'), 4000);
        }
        notificationClose.addEventListener('click', () => notification.classList.remove('show'));

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9]/g, '');
                if (input.value.length === 1 && index < inputs.length - 1) inputs[index + 1].focus();
                checkAndSubmit();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) inputs[index - 1].focus();
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,
                    '');
                pasteData.split('').forEach((char, i) => {
                    if (inputs[index + i]) inputs[index + i].value = char;
                });
                inputs[Math.min(inputs.length - 1, index + pasteData.length - 1)].focus();
                checkAndSubmit();
            });
        });

        let otpInterval, resendInterval;

        async function fetchRemaining() {
            try {
                const res = await fetch("{{ oRoute('otp.remaining') }}");
                const data = await res.json();

                const now = Math.floor(Date.now() / 1000);
                let otpRemaining = Math.max(0, data.otp_expires_at - now);
                let resendRemaining = Math.max(0, data.resend_available_at - now);

                startOtpTimer(otpRemaining);
                startResendTimer(resendRemaining);
            } catch (e) {
                console.error(e);
            }
        }

        function startOtpTimer(seconds) {
            clearInterval(otpInterval);
            otpInterval = setInterval(() => {
                if (seconds > 0) {
                    let m = Math.floor(seconds / 60);
                    let s = seconds % 60;
                    timerEl.textContent =
                        `OTP expires in ${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
                    seconds--;
                } else {
                    timerEl.textContent = 'OTP Expired';
                    clearInterval(otpInterval);
                }
            }, 1000);
        }

        function startResendTimer(seconds) {
            clearInterval(resendInterval);
            resendInterval = setInterval(() => {
                if (seconds > 0) {
                    resendBtn.disabled = true;
                    resendBtn.textContent = `Resend OTP (${seconds}s)`;
                    seconds--;
                } else {
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend OTP';
                    clearInterval(resendInterval);
                }
            }, 1000);
        }

        fetchRemaining();

        resendBtn.addEventListener('click', async () => {
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';
            try {
                const res = await fetch("{{ oRoute('otp.resend') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(data.success, 'success');
                    inputs.forEach(i => i.value = '');
                    updateHiddenInput();
                    inputs[0].focus();
                    otpRemaining = data.otp_remaining || 600;
                    resendRemaining = data.cooldown || 30;
                    startOtpTimer();
                    startResendTimer();
                } else showNotification(data.error || "Failed to resend OTP", 'error');
            } catch (e) {
                showNotification("Error while sending OTP", 'error');
            }
        });

        inputs[0].focus();
        fetchRemaining();
    </script>

    <style>
        .otp-input {
            aspect-ratio: 1/1;
            font-size: 1.3em;
            border-radius: 50%;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #ced4da;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 20px;
            border-radius: 12px;
            color: #fff;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 260px;
        }

        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .notification.success {
            background-color: #16a34a;
        }

        .notification.error {
            background-color: #dc2626;
        }
    </style>
</x-guest-layout>
