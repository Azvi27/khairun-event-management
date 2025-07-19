<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#181A26">
    <meta name="mobile-web-app-capable" content="yes">
    
    <title>@yield('title', 'Our Memories')</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts optimized for mobile -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Touch icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    
    {{-- ✅ FIXED CSS - No more scrolling issues --}}
    <link rel="stylesheet" href="{{ asset('css/khairun-fixed.css') }}">
    @stack('styles')
</head>
<body>
    <div class="PageContainer">
        <!-- Header Navigation -->
        <header class="header">
            <div class="nav-left">
                <div class="logo-box"></div>
                <a href="{{ route('dashboard') }}" class="nav-item">Our Memories</a>
            </div>
            <div class="nav-right">
                <a href="{{ route('memories.index') }}" class="nav-item">Gallery</a>
                <a href="{{ route('birthday-surprises.index') }}" class="nav-item">Countdown</a>
                <a href="{{ route('music.index') }}" class="nav-item">Music</a>
                <a href="{{ route('calendar') }}" class="nav-item">Calendar</a>
                <a href="{{ route('profile.index') }}" class="nav-item">Profile</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </header>

        <!-- Area Konten Utama -->
        <main class="content-area">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p class="footer-text">Copyright &copy; {{ date('Y') }} Khairun Project. All rights reserved.</p>
        </footer>
    </div>
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
    @stack('scripts')
</body>
</html>