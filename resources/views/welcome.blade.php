<!DOCTYPE html>
<html lang="id">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Our Memories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset dan Pengaturan Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Kontainer Utama Halaman dengan Enhanced Background */
        .HalamanSelamatDatang {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #181A26 0%, #1f212e 25%, #262840 75%, #181A26 100%);
            font-family: 'Inter', sans-serif;
        }

        /* Background dan Overlay Enhanced */
        .background-image {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            object-fit: cover;
            z-index: 1;
            transition: transform 20s ease-in-out;
            animation: slowZoom 20s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        .background-overlay {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, 
                rgba(24, 26, 38, 0.3) 0%, 
                rgba(44, 47, 63, 0.2) 25%, 
                rgba(0, 0, 0, 0.1) 50%, 
                rgba(24, 26, 38, 0.3) 100%);
            backdrop-filter: blur(1.5px);
            z-index: 2;
        }

        /* Floating particles effect */
        .background-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(140, 224, 255, 0.1) 1px, transparent 1px),
                radial-gradient(circle at 80% 80%, rgba(140, 224, 255, 0.08) 1px, transparent 1px),
                radial-gradient(circle at 40% 60%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100px 100px, 150px 150px, 200px 200px;
            animation: float 30s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(10px, -10px); }
            66% { transform: translate(-10px, 10px); }
        }

        /* Enhanced Header Elements */
        .header-container {
            width: 100%;
            height: 7.1vh;
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #181A26 0%, rgba(24, 26, 38, 0.95) 100%);
            z-index: 10;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(140, 224, 255, 0.1);
        }

        .logo {
            width: 1.45vw;
            height: 2.5vh;
            left: 2.5vw;
            top: 2.3vh;
            position: absolute;
            background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(140, 224, 255, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(140, 224, 255, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(140, 224, 255, 0.5); }
        }

        .brand-name {
            left: 5vw;
            top: 2.2vh;
            position: absolute;
            color: #D3D3D9;
            font-size: 1.85vh;
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced Main Content */
        .main-content-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 65vw;
            z-index: 5;
            text-align: center;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        .main-title {
            color: white;
            font-size: 4.6vh;
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 3vh;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #e8e8e8 50%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .main-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #8CE0FF, #6bd4ff, #4fc3ff);
            border-radius: 2px;
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.6; width: 80px; }
            50% { opacity: 1; width: 120px; }
        }

        .description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 2.3vh;
            font-weight: 400;
            max-width: 85%;
            margin: 0 auto 5vh auto;
            line-height: 1.6;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-family: 'Inter', sans-serif;
        }
        
        /* Enhanced Login Button */
        .main-login-btn {
            display: inline-block;
            background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 50%, #4fc3ff 100%);
            color: #181A26;
            font-size: 1.85vh;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            padding: 2vh 4vw;
            border-radius: 50px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(140, 224, 255, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .main-login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .main-login-btn:hover::before {
            left: 100%;
        }

        .main-login-btn:hover {
            background: linear-gradient(135deg, #6bd4ff 0%, #4fc3ff 50%, #3db8ff 100%);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(140, 224, 255, 0.4);
        }

        .main-login-btn:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Enhanced Polaroid Styles */
        .polaroid {
            position: absolute;
            width: 9vw;
            background: linear-gradient(135deg, #ffffff 0%, #f8f8f8 100%);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            padding: 0.6vw;
            padding-bottom: 2vw;
            z-index: 3;
            border-radius: 8px;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .polaroid::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #8CE0FF, #6bd4ff, #4fc3ff, #8CE0FF);
            border-radius: 10px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .polaroid:hover::before {
            opacity: 0.3;
        }

        .polaroid:hover {
            transform: scale(1.1) rotate(0deg) !important;
            box-shadow: 0 15px 40px rgba(140, 224, 255, 0.3);
            z-index: 4;
        }

        .polaroid img {
            width: 100%;
            height: auto;
            aspect-ratio: 1 / 1.1;
            object-fit: cover;
            display: block;
            border-radius: 4px;
            transition: transform 0.3s ease;
        }

        .polaroid:hover img {
            transform: scale(1.05);
        }

        .polaroid-1 { 
            left: 12vw; 
            top: 12vh; 
            transform: rotate(14deg);
            animation: floatLeft 6s ease-in-out infinite;
        }
        
        .polaroid-2 { 
            left: 15vw; 
            top: 55vh; 
            transform: rotate(-16deg);
            animation: floatLeft 6s ease-in-out infinite 1.5s;
        }
        
        .polaroid-3 { 
            right: 12vw; 
            top: 12vh; 
            transform: rotate(-14deg);
            animation: floatRight 6s ease-in-out infinite 3s;
        }
        
        .polaroid-4 { 
            right: 15vw; 
            top: 55vh; 
            transform: rotate(16deg);
            animation: floatRight 6s ease-in-out infinite 4.5s;
        }

        @keyframes floatLeft {
            0%, 100% { transform: rotate(14deg) translateY(0px); }
            50% { transform: rotate(14deg) translateY(-10px); }
        }

        @keyframes floatRight {
            0%, 100% { transform: rotate(-14deg) translateY(0px); }
            50% { transform: rotate(-14deg) translateY(-10px); }
        }

        /* Enhanced Footer */
        .footer-container {
            width: 100%;
            height: 24.8vh;
            position: absolute;
            bottom: 0;
            left: 0;
            background: linear-gradient(135deg, #181A26 0%, rgba(24, 26, 38, 0.95) 100%);
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(140, 224, 255, 0.1);
        }

        .footer-text {
            text-align: center;
            color: #D3D3D9;
            font-size: 2.3vh;
            width: 55vw;
            line-height: 1.6;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            opacity: 0.9;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Responsive Design Enhanced */
        @media (max-width: 1440px) {
            .main-title {
                font-size: 48px;
            }
            
            .description {
                font-size: 20px;
            }
            
            .main-login-btn {
                font-size: 18px;
                padding: 15px 35px;
            }
            
            .footer-text {
                font-size: 18px;
            }
            
            .polaroid {
                width: 120px;
            }
        }

        @media (max-width: 768px) {
            .main-content-container {
                width: 90vw;
            }
            
            .main-title {
                font-size: 32px;
                margin-bottom: 20px;
            }
            
            .description {
                font-size: 16px;
                margin-bottom: 30px;
            }
            
            .main-login-btn {
                font-size: 16px;
                padding: 12px 30px;
            }
            
            .footer-text {
                font-size: 16px;
                width: 80vw;
            }
            
            .polaroid {
                width: 80px;
                padding: 4px;
                padding-bottom: 15px;
            }
            
            .polaroid-1 { left: 5vw; top: 15vh; }
            .polaroid-2 { left: 8vw; top: 60vh; }
            .polaroid-3 { right: 5vw; top: 15vh; }
            .polaroid-4 { right: 8vw; top: 60vh; }
            
            .header-container {
                height: 60px;
            }
            
            .logo {
                width: 25px;
                height: 25px;
                top: 17px;
            }
            
            .brand-name {
                font-size: 16px;
                top: 20px;
            }
        }

        /* Loading animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .HalamanSelamatDatang {
            animation: fadeIn 1s ease-out;
        }

        /* Interactive glow effect on scroll */
        .main-content-container:hover .main-title {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3), 0 0 20px rgba(140, 224, 255, 0.3);
        }

        /* Enhanced scrolling indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 10vh;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 50px;
            border: 2px solid rgba(140, 224, 255, 0.5);
            border-radius: 25px;
            z-index: 5;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .scroll-indicator::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 8px;
            background: #8CE0FF;
            border-radius: 2px;
            animation: scrollDown 2s ease-in-out infinite;
        }

        @keyframes scrollDown {
            0%, 100% { opacity: 0; transform: translateX(-50%) translateY(0); }
            50% { opacity: 1; transform: translateX(-50%) translateY(10px); }
        }

        .scroll-indicator:hover {
            border-color: #8CE0FF;
            box-shadow: 0 0 20px rgba(140, 224, 255, 0.3);
        }
    </style>
    </head>
<body>
    <div class="HalamanSelamatDatang">
        <!-- Background -->
        <img data-layer="background" class="background-image" src="{{ asset('images/welcome/bg.png') }}" alt="Background">
        <div data-layer="overlay" class="background-overlay"></div>

        <!-- Header -->
        <div class="header-container">
            <div class="logo"></div>
            <div class="brand-name">Our Memories</div>
        </div>

        <!-- Polaroids -->
        <div class="polaroid polaroid-1">
            <img src="{{ asset('images/welcome/polaroid-1.png') }}" alt="Memory 1">
        </div>
        <div class="polaroid polaroid-2">
            <img src="{{ asset('images/welcome/polaroid-2.png') }}" alt="Memory 2">
        </div>
        <div class="polaroid polaroid-3">
            <img src="{{ asset('images/welcome/polaroid-3.png') }}" alt="Memory 3">
        </div>
        <div class="polaroid polaroid-4">
            <img src="{{ asset('images/welcome/polaroid-4.png') }}" alt="Memory 4">
        </div>

        <!-- Content -->
        <div class="main-content-container">
            <div class="main-title">
                A journey told in memories — stitched together by time, and always open for the stories you choose to keep.
            </div>
            <div class="description">
                Welcome to our private space where every moment matters and every memory lives forever.
            </div>
                    @auth
                <a href="{{ route('dashboard') }}" class="main-login-btn">Enter Dashboard</a>
                    @else
                <a href="{{ route('login') }}" class="main-login-btn">Login</a>
                    @endauth
                </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator" onclick="document.querySelector('.footer-container').scrollIntoView({behavior: 'smooth'})"></div>

        <!-- Footer -->
        <div class="footer-container">
            <div class="footer-text">
                Every pixel crafted with love, every line of code written for our story — this is where technology meets the heart.
                </div>
        </div>
    </div>

    <script>
        // Enhanced interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax effect for polaroids
            document.addEventListener('mousemove', function(e) {
                const polaroids = document.querySelectorAll('.polaroid');
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                polaroids.forEach((polaroid, index) => {
                    const speed = (index + 1) * 0.5;
                    const x = (mouseX - 0.5) * speed;
                    const y = (mouseY - 0.5) * speed;
                    
                    const currentTransform = polaroid.style.transform || '';
                    const rotation = currentTransform.match(/rotate\([^)]*\)/)?.[0] || '';
                    
                    polaroid.style.transform = `${rotation} translate(${x}px, ${y}px)`;
                });
            });

            // Enhanced click effects for polaroids
            document.querySelectorAll('.polaroid').forEach(polaroid => {
                polaroid.addEventListener('click', function() {
                    this.style.transform += ' scale(1.2)';
                    setTimeout(() => {
                        this.style.transform = this.style.transform.replace(' scale(1.2)', '');
                    }, 300);
                });
            });

            // Add smooth scroll behavior
            document.querySelector('.main-login-btn').addEventListener('click', function(e) {
                this.style.transform += ' scale(0.95)';
                setTimeout(() => {
                    this.style.transform = this.style.transform.replace(' scale(0.95)', '');
                }, 150);
            });
        });
    </script>
    </body>
</html>