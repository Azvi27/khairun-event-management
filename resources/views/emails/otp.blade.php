<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .otp-code {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            letter-spacing: 5px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">💝 Website Khairun</div>
            <h2>Kode Verifikasi Login</h2>
        </div>

        <!-- Greeting -->
        <p>Halo <strong>{{ $userName }}</strong>! 👋</p>
        
        <p>Kami menerima permintaan login ke akun Website Khairun Anda. Untuk melanjutkan, silakan gunakan kode OTP berikut:</p>

        <!-- OTP Code -->
        <div class="otp-code">
            {{ $otpCode }}
        </div>

        <!-- Instructions -->
        <p><strong>Cara menggunakan:</strong></p>
        <ol>
            <li>Kembali ke halaman login Website Khairun</li>
            <li>Masukkan kode OTP di atas</li>
            <li>Klik "Verifikasi" untuk melanjutkan</li>
        </ol>

        <!-- Warning -->
        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul>
                <li>Kode ini <strong>berlaku selama 10 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapa pun</li>
                <li>Jika Anda tidak melakukan login, abaikan email ini</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem Website Khairun.</p>
            <p>💝 <em>Untuk kenangan indah yang tak terlupakan</em></p>
        </div>
    </div>
</body>
</html>