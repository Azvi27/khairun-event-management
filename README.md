# 🌟 Khairun - Personal Memory & Event Management System

Sistem manajemen kenangan dan acara pribadi yang membantu Anda mengorganisir momen-momen berharga dalam hidup dengan teknologi modern dan keamanan berlapis.

## 📖 Daftar Isi

- [Tentang Khairun](#-tentang-khairun)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#️-teknologi)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Halaman-Halaman Sistem](#-halaman-halaman-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#️-konfigurasi)
- [Struktur Project](#-struktur-project)
- [Keamanan](#-keamanan)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Kontribusi](#-kontribusi)

## 🎯 Tentang Khairun

Khairun adalah platform komprehensif yang dirancang untuk membantu pengguna:
- 💭 Menyimpan dan mengorganisir kenangan pribadi dengan mudah
- 📅 Mengelola acara dan jadwal dengan sistem kalender terintegrasi
- 🎂 Memberikan kejutan ulang tahun yang terjadwal secara otomatis
- 🎵 Mengintegrasikan musik dari Spotify untuk memperkaya pengalaman
- 🔐 Mengakses semua fitur dengan sistem autentikasi yang aman

## ✨ Fitur Utama

### 🔐 **Sistem Autentikasi Berlapis (2FA)**
- **Email + Password**: Validasi kredensial tradisional
- **OTP Verification**: Kode 6 digit via email (berlaku 10 menit)
- **Session Management**: Keamanan session dengan Laravel Sanctum
- **Rate Limiting**: Perlindungan dari serangan brute force

### 🏠 **Dashboard Interaktif**
- Overview lengkap kenangan dan event terbaru
- Widget statistik personal (jumlah kenangan, event mendatang)
- Quick actions untuk membuat konten baru
- Navigasi terpusat ke semua modul sistem

### 💭 **Manajemen Kenangan**
- Timeline chronological dengan infinite scroll
- Upload foto dengan drag-and-drop interface
- Integrasi musik Spotify untuk setiap kenangan
- Search dan filter berdasarkan tanggal dan konten
- Export data untuk portabilitas

### 📅 **Sistem Kalender & Event**
- Tampilan kalender interaktif (bulanan, mingguan, harian)
- Event berulang dengan pola fleksibel
- Kolaborasi multi-user dengan tracking partisipan
- Notifikasi dan reminder otomatis

### 🎂 **Birthday Surprise System**
- Sistem kejutan terjadwal dengan countdown timer
- Konten multimedia (teks, gambar, musik)
- Template library untuk pembuatan cepat
- Archive historical surprises

### 🎵 **Integrasi Musik Spotify**
- Search track dengan autocomplete
- Preview audio dengan embedded player
- Attachment musik ke kenangan dan event
- Rekomendasi playlist personal

### 👤 **Manajemen Profil**
- Pengaturan privasi granular
- Kustomisasi notifikasi
- Keamanan akun dengan monitoring aktivitas
- Data management dan export capabilities

## 🛠️ Teknologi

### Backend Stack
- **Framework**: Laravel 12 - Framework PHP modern dengan fitur lengkap
- **Language**: PHP 8.2+ - Bahasa pemrograman dengan performa tinggi
- **Database**: SQLite (development) / MySQL (production)
- **ORM**: Eloquent - Object-Relational Mapping yang elegant
- **Authentication**: Laravel Session + Custom OTP
- **Email**: SMTP - Reliable email delivery untuk OTP
- **HTTP Client**: Guzzle - Untuk integrasi API eksternal

### Frontend Stack
- **Template Engine**: Blade - Server-side rendering dengan component system
- **CSS Framework**: Tailwind CSS - Utility-first styling approach
- **JavaScript**: Alpine.js - Lightweight reactive framework
- **Build Tool**: Vite - Modern asset bundling dengan hot reloading
- **Icons**: Custom SVG - Scalable vector graphics

### External Services
- **Music API**: Spotify Web API - Integrasi musik dan track search
- **File Storage**: AWS S3 / Local storage - Fleksible media management
- **Email Service**: SMTP providers - Delivery OTP dan notifikasi

### Development Tools
- **Dependency Manager**: Composer (PHP), NPM (Node.js)
- **Testing**: PHPUnit - Comprehensive testing framework
- **Code Quality**: Laravel Pint - Automated code formatting
- **Version Control**: Git - Source code management

## 🏗️ Arsitektur Sistem

### Pola Arsitektur
Khairun menggunakan **MVC (Model-View-Controller)** pattern dengan **Service Layer**:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Controllers   │───▶│    Services     │───▶│     Models      │
│  (HTTP Layer)   │    │ (Business Logic)│    │ (Data Layer)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Views       │    │   External APIs │    │    Database     │
│ (Blade Templates)│    │ (Spotify, SMTP) │    │   (SQLite)      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Database Schema
**Entitas Utama:**
- **Users**: Manajemen pengguna dengan OTP authentication
- **Memories**: Penyimpanan kenangan dengan media attachments
- **Events**: Event management dengan multi-user collaboration
- **Birthday Surprises**: Sistem kejutan terjadwal
- **Event Participants**: Many-to-many relationship untuk event collaboration

## 📱 Halaman-Halaman Sistem

### 🔐 **Modul Autentikasi**
- **Login** - Form email + password dengan validasi real-time
- **Verifikasi OTP** - Input kode 6 digit dengan countdown timer
- **Resend OTP** - Fungsi kirim ulang dengan rate limiting

### 🏠 **Halaman Utama**
- **Welcome** - Landing page dengan pengenalan sistem
- **Dashboard** - Control center dengan overview dan quick actions

### 💭 **Modul Kenangan**
- **Timeline Kenangan** - Chronological view dengan infinite scroll
- **Buat Kenangan** - Form dengan upload foto dan pilih musik
- **Edit Kenangan** - Modifikasi konten existing
- **Detail Kenangan** - Full view dengan semua media
- **Search & Filter** - Pencarian berdasarkan tanggal dan konten

### 📅 **Modul Kalender & Event**
- **Kalender Interaktif** - Monthly/weekly/daily view
- **Daftar Event** - List view dengan filter dan sorting
- **Buat Event** - Form dengan undangan peserta
- **Edit Event** - Modifikasi event existing
- **Detail Event** - Info lengkap dan manajemen peserta

### 🎂 **Modul Birthday Surprise**
- **Daftar Surprise** - Archive semua kejutan
- **Buat Surprise** - Form dengan multimedia content
- **Preview Surprise** - Tampilan dengan countdown
- **Reveal Animation** - Animasi pembukaan kejutan

### 🎵 **Modul Musik**
- **Search Spotify** - Interface pencarian dengan autocomplete
- **Music Player** - Audio player dengan controls
- **Playlist Management** - Organize musik favorit

### 👤 **Modul Profil**
- **Profile View** - Informasi personal dan statistik
- **Edit Profile** - Form update data personal
- **Settings** - Konfigurasi notifikasi dan privasi
- **Security** - Manajemen keamanan akun

## 🚀 Instalasi

### Persyaratan Sistem
- **PHP**: >= 8.2 dengan extensions (mbstring, xml, ctype, json, bcmath, fileinfo)
- **Composer**: >= 2.0 untuk dependency management
- **Node.js**: >= 18.0 untuk asset compilation
- **NPM**: >= 9.0 untuk package management
- **Database**: SQLite 3.x / MySQL 8.0+ / PostgreSQL 13.0+

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/username/khairun.git
cd khairun
```

#### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Database Setup
```bash
# Untuk SQLite (Development)
touch database/database.sqlite

# Jalankan migrasi
php artisan migrate

# Seed data awal (opsional)
php artisan db:seed
```

#### 5. Build Assets
```bash
# Production build
npm run build

# Atau untuk development dengan hot reload
npm run dev
```

#### 6. Jalankan Aplikasi
```bash
# Start development server
php artisan serve

# Aplikasi akan tersedia di http://localhost:8000
```

## ⚙️ Konfigurasi

### Environment Variables (.env)

#### Application Settings
```env
APP_NAME=Khairun
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000
```

#### Database Configuration
```env
# SQLite (Development)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# MySQL (Production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khairun
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Email Configuration (untuk OTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Khairun System"
```

#### Spotify API Configuration
```env
SPOTIFY_CLIENT_ID=your_spotify_client_id
SPOTIFY_CLIENT_SECRET=your_spotify_client_secret
```

### Setup External Services

#### Spotify API Setup
1. Kunjungi [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
2. Buat aplikasi baru
3. Dapatkan Client ID dan Client Secret
4. Tambahkan ke file `.env`

#### Email SMTP Setup
**Gmail:**
1. Aktifkan 2FA di akun Google
2. Generate App Password
3. Gunakan App Password di `.env`

**Outlook:**
```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your_email@outlook.com
MAIL_PASSWORD=your_password
```

## 📁 Struktur Project

```
khairun/
├── app/                     # Aplikasi utama
│   ├── Http/Controllers/    # Pengendali request
│   │   ├── Auth/           # Autentikasi controllers
│   │   ├── MemoryController.php
│   │   ├── EventController.php
│   │   └── BirthdaySurpriseController.php
│   ├── Models/             # Model database
│   │   ├── User.php        # User model dengan OTP methods
│   │   ├── Memory.php      # Memory model
│   │   ├── Event.php       # Event model
│   │   └── BirthdaySurprise.php
│   ├── Services/           # Business logic
│   │   ├── SpotifyService.php
│   │   └── StorageService.php
│   ├── Mail/              # Email templates
│   │   └── OTPMail.php
│   └── Providers/         # Service providers
│
├── resources/              # Frontend resources
│   ├── views/             # Blade templates
│   │   ├── auth/          # Authentication views
│   │   ├── dashboard.blade.php
│   │   ├── memories/      # Memory management views
│   │   ├── events/        # Event management views
│   │   ├── birthday-surprises/
│   │   ├── music/         # Music integration views
│   │   ├── profile/       # Profile management views
│   │   └── layouts/       # Layout templates
│   ├── css/              # Styling
│   └── js/               # JavaScript
│
├── database/              # Database
│   ├── migrations/        # Database schema
│   ├── seeders/          # Data seeders
│   └── factories/        # Model factories
│
├── routes/               # Routing
│   ├── web.php           # Web routes
│   ├── api.php           # API routes
│   └── auth.php          # Authentication routes
│
├── public/               # Public assets
│   ├── css/              # Compiled CSS
│   ├── js/               # Compiled JavaScript
│   ├── images/           # Static images
│   └── build/            # Vite build assets
│
├── config/               # Configuration
│   ├── app.php           # App configuration
│   ├── database.php      # Database configuration
│   ├── mail.php          # Mail configuration
│   └── services.php      # External services
│
├── tests/                # Testing
│   ├── Feature/          # Feature tests
│   └── Unit/             # Unit tests
│
├── Desain/               # HTML prototypes
│   ├── Dashboard/        # Dashboard designs
│   ├── Login/            # Login designs
│   ├── Kenangan/         # Memory designs
│   ├── Kalender/         # Calendar designs
│   └── Profil/           # Profile designs
│
└── Dokumen/              # Project documentation
    ├── Dokumen Plan Khairun Project.txt
    └── Khairun-1.5.md
```

## 🔒 Keamanan

### Authentication & Authorization
- **2FA Authentication**: Email + Password + OTP
- **Session Security**: Secure session handling dengan Laravel
- **Rate Limiting**: Protection terhadap brute force attacks
- **Token Management**: Secure OTP generation dengan expiry time

### Data Protection
- **Input Validation**: Comprehensive form request validation
- **XSS Prevention**: Output escaping dengan Blade templating
- **CSRF Protection**: Cross-site request forgery prevention
- **SQL Injection Protection**: Parameterized queries via Eloquent ORM
- **File Upload Security**: MIME type validation dan secure storage

### Privacy & Access Control
- **User-owned Content**: Strict ownership validation
- **Privacy Settings**: Granular content visibility control
- **Data Encryption**: Sensitive data protection at rest
- **Audit Logging**: Activity tracking untuk security monitoring

## 🧪 Testing

### Menjalankan Tests
```bash
# Jalankan semua tests
php artisan test

# Jalankan specific test
php artisan test --filter=AuthenticationTest

# Jalankan dengan coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **Integration Tests**: Service integration verification

## 🚀 Deployment

### Production Checklist
- [ ] Environment variables configured
- [ ] Database optimized dengan proper indexing
- [ ] SSL certificate installed
- [ ] Caching enabled (Redis/Memcached)
- [ ] Log rotation configured
- [ ] Backup strategy implemented
- [ ] Monitoring tools setup

### Deployment Options

#### Shared Hosting
```bash
# Upload files via FTP/cPanel
# Set document root ke /public
# Configure .env file
```

#### VPS/Cloud Server
```bash
# Install dependencies
sudo apt update
sudo apt install php8.2 nginx mysql-server

# Configure web server
# Setup SSL certificate
# Configure database
```

#### Docker Deployment
```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage
```

## 🤝 Kontribusi

### Development Workflow
1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Document new features
- Use meaningful commit messages

### Bug Reports
Gunakan GitHub Issues dengan template:
- **Bug Description**: Deskripsi detail bug
- **Steps to Reproduce**: Langkah-langkah reproduksi
- **Expected Behavior**: Behavior yang diharapkan
- **Screenshots**: Screenshot jika diperlukan
- **Environment**: OS, PHP version, browser

---

## 📄 Lisensi

Khairun dilisensikan di bawah [MIT License](LICENSE).

## 📞 Kontak

- **Developer**: [Your Name]
- **Email**: your.email@example.com
- **GitHub**: [@yourusername](https://github.com/yourusername)
- **Website**: [https://khairun.example.com](https://khairun.example.com)

---

**Khairun** - Sistem modern untuk mengorganisir kenangan dan acara pribadi dengan teknologi terkini dan arsitektur yang scalable.

*"Setiap momen berharga layak untuk diingat dan dirayakan."* ✨
