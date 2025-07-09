<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Our Memories')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=DM+Serif+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/khairun.css') }}">
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
    @stack('scripts')
</body>
</html>