<!DOCTYPE html>
<html lang="id">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Our Memories</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=DM+Serif+Text&display=swap" rel="stylesheet">
    <style>
        /* Reset dan Pengaturan Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Kontainer Utama Halaman */
        .HalamanSelamatDatang {
            width: 100%;
            height: 100%;
            position: relative;
            background: #181A26;
            font-family: 'Poppins', sans-serif;
        }

        /* Background dan Overlay */
        .background-image {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            object-fit: cover;
            z-index: 1;
        }

        .background-overlay {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.20);
            backdrop-filter: blur(1px);
            z-index: 2;
        }

        /* Header Elements */
        .header-container {
            width: 100%;
            height: 7.1vh;
            position: absolute;
            top: 0;
            left: 0;
            background: #181A26;
            z-index: 10;
        }

        .logo {
            width: 1.45vw;
            height: 2.5vh;
            left: 2.5vw;
            top: 2.3vh;
            position: absolute;
            background: #8CE0FF;
        }

        .brand-name {
            left: 5vw;
            top: 2.2vh;
            position: absolute;
            color: #D3D3D9;
            font-size: 1.85vh;
        }

        /* Main Content */
        .main-content-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60vw;
            z-index: 5;
            text-align: center;
        }

        .main-title {
            color: white;
            font-size: 4.6vh;
            font-family: 'DM Serif Text', serif;
            font-weight: 400;
            line-height: 1.2;
            margin-bottom: 3vh;
        }

        .description {
            color: white;
            font-size: 2.3vh;
            font-weight: 400;
            max-width: 80%;
            margin: 0 auto 4vh auto;
        }
        
        .main-login-btn {
            display: inline-block;
            background: #8CE0FF;
            color: #181A26;
            font-size: 1.85vh;
            font-weight: 500;
            padding: 1.5vh 3vw;
            border-radius: 5vh;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .main-login-btn:hover {
            background: #6bd4ff;
            transform: translateY(-2px);
        }

        /* Polaroid Styles */
        .polaroid {
            position: absolute;
            width: 8vw;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 0.5vw;
            padding-bottom: 1.5vw;
            z-index: 3;
        }

        .polaroid img {
            width: 100%;
            height: auto;
            aspect-ratio: 1 / 1.1;
            object-fit: cover;
            display: block;
        }

        .polaroid-1 { left: 14vw; top: 12vh; transform: rotate(14deg); }
        .polaroid-2 { left: 17vw; top: 50vh; transform: rotate(-16deg); }
        .polaroid-3 { right: 14vw; top: 12vh; transform: rotate(-14deg); }
        .polaroid-4 { right: 17vw; top: 50vh; transform: rotate(16deg); }

        /* Footer */
        .footer-container {
            width: 100%;
            height: 24.8vh;
            position: absolute;
            bottom: 0;
            left: 0;
            background: #181A26;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer-text {
            text-align: center;
            color: #D3D3D9;
            font-size: 2.3vh;
            width: 50vw;
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

        <!-- Footer -->
        <div class="footer-container">
            <div class="footer-text">
                Every pixel crafted with love, every line of code written for our story — this is where technology meets the heart.
                </div>
        </div>
    </div>
    </body>
</html>