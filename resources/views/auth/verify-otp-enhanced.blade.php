<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Our Memories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/otp-enhanced.css') }}" rel="stylesheet">
    <style>
        /* 6-Digit Input Style */
        .otp-digits-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: var(--space-lg);
        }
        
        .otp-digit-input {
            width: 50px;
            height: 60px;
            background: rgba(24, 26, 38, 0.5);
            border: 2px solid rgba(140, 224, 255, 0.3);
            border-radius: 12px;
            color: var(--color-text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            transition: all var(--transition-normal);
            font-family: 'Monaco', 'Courier New', monospace;
        }
        
        .otp-digit-input:focus {
            outline: none;
            border-color: var(--color-primary);
            background: rgba(24, 26, 38, 0.7);
            box-shadow: 0 0 0 4px rgba(140, 224, 255, 0.15);
            transform: translateY(-2px) scale(1.05);
        }
        
        .otp-digit-input.filled {
            border-color: var(--color-primary);
            background: rgba(140, 224, 255, 0.1);
        }
        
        @media (max-width: 480px) {
            .otp-digits-container {
                gap: 8px;
            }
            
            .otp-digit-input {
                width: 45px;
                height: 55px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="otp-page">
        <!-- Background Image -->
        <img class="background-image" src="{{ asset('images/login/bg.png') }}" alt="Background">
        <div class="overlay"></div>

        <!-- Header -->
        <header class="header">
            <div class="logo-square"></div>
            <div class="brand-name">Our Memories</div>
        </header>

        <!-- Main Content Wrapper -->
        <div class="content-wrapper">
            <!-- OTP Form -->
            <div class="otp-container">
            <h1 class="otp-title">Verify Your Identity</h1>
            <p class="otp-subtitle">
                Kami telah mengirim kode OTP 6 digit ke email Anda. Silakan masukkan kode tersebut untuk melanjutkan login.
            </p>
            <p class="otp-instructions">
                📧 Kode OTP telah dikirim ke email Anda
            </p>

            <!-- Timer Warning -->
            <div class="timer-warning">
                <span class="icon">⏱️</span>
                <span id="timer-text">Kode berlaku selama <strong id="countdown">10:00</strong></span>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="success-message">
                    ✅ {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-message">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify.post') }}" id="otpForm">
                @csrf
                
                <!-- Hidden input for combined OTP -->
                <input type="hidden" name="otp" id="otpValue">

                <!-- 6 Individual Digit Inputs -->
                <div class="otp-label">Masukkan Kode OTP (6 Digit)</div>
                <div class="otp-digits-container">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="0" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="2" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="3" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="4" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit-input" data-index="5" pattern="[0-9]" inputmode="numeric">
                </div>

                @error('otp')
                    <div class="error-message">⚠️ {{ $message }}</div>
                @enderror

                <!-- Security Tips -->
                <div class="security-tips">
                    <div class="tips-title">
                        <span>🔒</span> Tips Keamanan
                    </div>
                    <ul class="tips-list">
                        <li>Jangan bagikan kode OTP kepada siapa pun</li>
                        <li>Pastikan Anda berada di website yang benar</li>
                        <li>Kode OTP hanya berlaku untuk satu kali penggunaan</li>
                    </ul>
                </div>

                <div class="button-container">
                    <button type="submit" class="verify-button" id="verifyBtn" disabled>
                        🔐 VERIFIKASI OTP
                    </button>
                    <a href="{{ route('login') }}" class="back-button">
                        ← Kembali ke Login
                    </a>
                </div>
            </form>

            <!-- Resend OTP -->
            <div class="resend-container">
                <div class="resend-text">Tidak menerima kode OTP?</div>
                <a href="#" class="resend-link" id="resendLink" onclick="resendOTP(event)">
                    📧 Kirim Ulang Kode OTP
                </a>
            </div>
        </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-text">
                Where hearts connect through pixels and dreams become digital reality.
            </div>
        </footer>
    </div>

    <script>
        // Countdown Timer
        let timeLeft = 600; // 10 minutes in seconds
        const countdownEl = document.getElementById('countdown');
        const timerTextEl = document.getElementById('timer-text');
        
        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 60) {
                countdownEl.style.color = '#FF6B6B';
                timerTextEl.innerHTML = `⚠️ Kode akan segera kadaluarsa! <strong id="countdown">${countdownEl.textContent}</strong>`;
            }
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Kadaluarsa';
                timerTextEl.innerHTML = '❌ Kode OTP telah kadaluarsa';
                document.getElementById('verifyBtn').disabled = true;
                document.getElementById('resendLink').style.fontWeight = 'bold';
            }
            
            timeLeft--;
        }
        
        const timer = setInterval(updateCountdown, 1000);
        updateCountdown();

        // 6-Digit OTP Input Handler
        const inputs = document.querySelectorAll('.otp-digit-input');
        const verifyBtn = document.getElementById('verifyBtn');
        const otpValue = document.getElementById('otpValue');
        const form = document.getElementById('otpForm');
        
        inputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                
                // Only allow numbers
                if (!/^\d$/.test(value) && value !== '') {
                    e.target.value = '';
                    return;
                }
                
                // Add filled class
                if (value) {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
                
                // Auto focus next input
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Check if all inputs are filled
                checkComplete();
            });
            
            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                        inputs[i].classList.add('filled');
                    }
                });
                
                // Focus last filled or next empty
                const lastFilledIndex = Math.min(digits.length - 1, 5);
                if (inputs[lastFilledIndex]) {
                    inputs[lastFilledIndex].focus();
                }
                
                checkComplete();
            });
        });
        
        // Check if all inputs are complete
        function checkComplete() {
            const values = Array.from(inputs).map(input => input.value);
            const isComplete = values.every(val => val !== '');
            
            if (isComplete) {
                otpValue.value = values.join('');
                verifyBtn.disabled = false;
                
                // Auto submit after a short delay
                setTimeout(() => {
                    if (values.every(val => val !== '')) {
                        submitForm();
                    }
                }, 500);
            } else {
                otpValue.value = '';
                verifyBtn.disabled = true;
            }
        }
        
        // Submit form
        function submitForm() {
            verifyBtn.innerHTML = '⏳ Memverifikasi...';
            verifyBtn.disabled = true;
            verifyBtn.classList.add('loading');
            
            // Submit the form
            form.submit();
            
            // Backup redirect after delay
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 3000);
        }
        
        // Manual submit
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm();
        });
        
        // Resend OTP
        function resendOTP(e) {
            e.preventDefault();
            const link = e.target;
            
            // Disable link temporarily
            link.style.pointerEvents = 'none';
            link.innerHTML = '⏳ Mengirim ulang...';
            
            // Reset timer
            timeLeft = 600;
            
            // Reset inputs
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
            inputs[0].focus();
            
            // Simulate resend (replace with actual API call)
            setTimeout(() => {
                link.style.pointerEvents = 'auto';
                link.innerHTML = '📧 Kirim Ulang Kode OTP';
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'success-message';
                successMsg.innerHTML = '✅ Kode OTP baru telah dikirim ke email Anda!';
                form.insertBefore(successMsg, form.firstChild);
                
                setTimeout(() => {
                    successMsg.remove();
                }, 5000);
            }, 2000);
        }
        
        // Focus first input on load
        inputs[0].focus();
        
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html> 