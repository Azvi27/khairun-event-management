<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ADD THIS -->
    <title>Verify OTP - Our Memories</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=DM+Serif+Text&display=swap" rel="stylesheet">
    <link href="{{ asset('css/otp.css') }}" rel="stylesheet">
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

        <!-- OTP Form -->
        <div class="otp-container">
            <h1 class="otp-title">Verify Your Identity</h1>
            <p class="otp-subtitle">
                Kami telah mengirim kode OTP 6 digit ke email Anda. Silakan masukkan kode tersebut untuk melanjutkan login.
            </p>
            <p class="otp-instructions">
                Kode OTP telah dikirim ke email Anda. Silakan cek inbox/spam.
            </p>

            <!-- Timer Warning -->
            <div class="timer-warning">
                <span class="icon">⏰</span>
                <span>Kode OTP berlaku selama 10 menit</span>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify.post') }}">
                @csrf
                
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="otp-input-container">
                    <label for="otp" class="otp-label">Kode OTP (6 Digit)</label>
                    <input id="otp" 
                           name="otp" 
                           type="text" 
                           class="otp-input" 
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required 
                           autofocus 
                           autocomplete="one-time-code"
                           placeholder="123456">
                    @error('otp_code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Security Tips -->
                <div class="security-tips">
                    <div class="tips-title">
                        <span>💡</span> Tips Keamanan
                    </div>
                    <ul class="tips-list">
                        <li>Jangan bagikan kode OTP kepada siapa pun</li>
                        <li>Pastikan Anda berada di website yang benar</li>
                        <li>Kode OTP hanya berlaku untuk satu kali penggunaan</li>
                    </ul>
                </div>

                <div class="button-container">
                    <button type="submit" class="verify-button" id="verifyBtn">
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
                <a href="#" class="resend-link" onclick="resendOTP()">
                    📧 Kirim Ulang Kode OTP
                </a>
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
        // Auto format OTP input
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Auto submit dan force redirect when 6 digits entered
        document.getElementById('otp').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                setTimeout(() => {
                    if (this.value.length === 6) {
                        // Add loading indicator
                        const button = document.querySelector('.verify-button');
                        button.innerHTML = '⏳ Verifying...';
                        button.disabled = true;
                        
                        // Submit form
                        this.form.submit();
                        
                        // Force redirect after 3 seconds as backup
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 3000);
                    }
                }, 500);
            }
        });

        // Manual submit handling
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = document.querySelector('.verify-button');
            button.innerHTML = '⏳ Processing...';
            button.disabled = true;
            
            // Backup redirect
            setTimeout(() => {
                if (!document.hidden) {
                    window.location.href = '/dashboard';
                }
            }, 4000);
        });

        // Resend OTP function
        function resendOTP() {
            alert('Fitur kirim ulang OTP akan segera tersedia!');
        }

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>