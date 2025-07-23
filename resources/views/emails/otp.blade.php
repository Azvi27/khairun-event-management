<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode OTP - Khairun</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border: 2px dashed #667eea;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Kode Verifikasi OTP</h1>
            <p>Khairun - Our Memories</p>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Gunakan kode OTP berikut untuk login ke akun Anda:</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <p><strong>⏰ Kode berlaku selama 10 menit</strong></p>
            <p>Jika Anda tidak mencoba login, abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>💝 Email ini dikirim otomatis dari sistem Khairun</p>
            <p>Demi keamanan, jangan bagikan kode ini kepada siapapun</p>
        </div>
    </div>
</body>
</html>