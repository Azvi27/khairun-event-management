<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎁 Your Birthday Surprise is Ready!</title>
    <style>
        /* Email-safe CSS styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .email-header {
            background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
            padding: 40px 30px;
            text-align: center;
            color: #181A26;
        }
        
        .surprise-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }
        
        .email-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            margin-bottom: 10px;
        }
        
        .email-subtitle {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.8;
        }
        
        .email-content {
            padding: 40px 30px;
        }
        
        .surprise-details {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            border-left: 5px solid #8CE0FF;
        }
        
        .surprise-meta {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .content-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .surprise-info {
            color: #64748b;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        
        .sender-info {
            background: rgba(140, 224, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .sender-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #181A26;
            margin-bottom: 5px;
        }
        
        .sender-message {
            color: #64748b;
            font-style: italic;
        }
        
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
            color: #181A26;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(140, 224, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(140, 224, 255, 0.4);
        }
        
        .email-footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            color: #64748b;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        
        .love-message {
            color: #8CE0FF;
            font-weight: 600;
            margin-top: 15px;
        }
        
        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px 10px;
                border-radius: 15px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-title {
                font-size: 1.5rem;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .surprise-details {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <span class="surprise-icon">🎁</span>
            <h1 class="email-title">Your Surprise is Ready!</h1>
            <p class="email-subtitle">Someone special prepared something for you</p>
        </div>
        
        <!-- Content Section -->
        <div class="email-content">
            <p>Hi <strong>{{ $receiver->name }}</strong>,</p>
            
            <p>Great news! A special birthday surprise that was prepared for you is now ready to be revealed! 🎉</p>
            
            <!-- Surprise Details -->
            <div class="surprise-details">
                <div class="surprise-meta">
                    <span class="content-icon">{{ $contentIcon }}</span>
                    <div>
                        <div class="surprise-info"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $surprise->content_type)) }}</div>
                        <div class="surprise-info"><strong>Revealed on:</strong> {{ $revealDate }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Sender Information -->
            <div class="sender-info">
                <div class="sender-name">💝 From {{ $sender->name }}</div>
                <div class="sender-message">Made with love just for you</div>
            </div>
            
            <!-- Call to Action -->
            <div class="cta-section">
                <a href="{{ $surpriseUrl }}" class="cta-button">
                    👁️ View Your Surprise
                </a>
            </div>
            
            <p>This surprise was carefully timed and prepared especially for you. Click the button above to see what {{ $sender->name }} has prepared! ✨</p>
            
            <p><em>Remember: The best surprises are meant to bring joy and strengthen the bonds we share. Enjoy this special moment! 💕</em></p>
        </div>
        
        <!-- Footer Section -->
        <div class="email-footer">
            <p class="footer-text">This email was sent from Khairun</p>
            <p class="footer-text">A private space for memories and special moments</p>
            <p class="love-message">Made with 💝 for Azvi & Khairun</p>
        </div>
    </div>
</body>
</html> 