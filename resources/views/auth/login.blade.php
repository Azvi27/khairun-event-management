<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Our Memories</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=DM+Serif+Text&display=swap" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="Login">
        <!-- Background Image -->
        <img class="background-image" src="{{ asset('images/login/bg.png') }}" alt="Background">
        <div class="overlay"></div>

        <!-- Header -->
        <header class="header">
            <div class="logo-square"></div>
            <div class="brand-name">Our Memories</div>
        </header>

        <!-- Login Form -->
        <div class="login-container">
            <h1 class="login-title">Log in to your Account</h1>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="input-container">
                    <div class="input-field">
                        <label for="email" class="input-label">Email Address</label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               class="form-input" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="Enter your email address">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="input-divider"></div>
                    
                    <div class="input-field">
                        <label for="password" class="input-label">Password</label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               class="form-input" 
                               required 
                               autocomplete="current-password"
                               placeholder="Enter your password">
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if (Route::has('password.request'))
                    <a class="forgot-password" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif
                
                <button type="submit" class="login-button">Log in</button>
            </form>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-text">
                Where hearts connect through pixels and dreams become digital reality.
            </div>
        </footer>
    </div>
</body>
</html>