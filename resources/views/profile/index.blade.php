@extends('layouts.khairun')

@section('title', 'Profil')

@push('styles')
<style>
.profile-container {
    max-width: 900px;
    margin: 0 auto;
}

.profile-header {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 40px;
    border: 2px solid rgba(140, 224, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(140, 224, 255, 0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.profile-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
    position: relative;
    z-index: 2;
}

.profile-subtitle {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.9;
    position: relative;
    z-index: 2;
}

.profile-avatar-section {
    background: rgba(140, 224, 255, 0.1);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 40px;
    text-align: center;
    border: 1px solid rgba(140, 224, 255, 0.3);
    position: relative;
}

.avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #8CE0FF;
    box-shadow: 0 8px 25px rgba(140, 224, 255, 0.4);
    transition: all 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(140, 224, 255, 0.6);
}

.avatar-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: #8CE0FF;
    color: #181A26;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
}

.profile-name {
    color: #181A26;;
    font-family: 'DM Serif Text', serif;
    font-size: 1.8rem;
    margin-bottom: 5px;
}

.profile-email {
    color: #D3D3D9;
    font-size: 1rem;
    opacity: 0.8;
}

.profile-sections {
    display: grid;
    gap: 30px;
}

.profile-section {
    background: #181A26;
    border-radius: 20px;
    padding: 40px;
    border: 1px solid rgba(140, 224, 255, 0.2);
    transition: all 0.3s ease;
}

.profile-section:hover {
    border-color: rgba(140, 224, 255, 0.4);
    transform: translateY(-2px);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(140, 224, 255, 0.2);
}

.section-icon {
    background: rgba(140, 224, 255, 0.2);
    color: #8CE0FF;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.2rem;
}

.section-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 1.5rem;
    margin: 0;
}

.flash-message {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
}

.flash-success {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
}

.flash-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.tab-navigation {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    padding: 5px;
    background: rgba(52, 54, 70, 0.6);
    border-radius: 15px;
}

.tab-btn {
    flex: 1;
    padding: 12px 20px;
    border-radius: 10px;
    background: transparent;
    color: #D3D3D9;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
}

.tab-btn.active {
    background: #8CE0FF;
    color: #181A26;
}

.tab-btn:hover:not(.active) {
    background: rgba(140, 224, 255, 0.1);
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
        <h1 class="profile-title">👤 Profil Saya</h1>
        <p class="profile-subtitle">Kelola informasi pribadi dan keamanan akun Anda</p>
    </div>

    <!-- Flash Messages -->
    @if(session('status') === 'profile-updated')
        <div class="flash-message flash-success">
            ✅ Profil berhasil diperbarui!
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="flash-message flash-success">
            🔒 Password berhasil diubah!
        </div>
    @endif

    @if($errors->any())
        <div class="flash-message flash-error">
            ❌ Ada kesalahan: {{ $errors->first() }}
        </div>
    @endif

    <!-- Profile Avatar Section -->
    <div class="profile-avatar-section">
        <div class="avatar-container">
        <img src="{{ Auth::user()->profile_photo_path ? app('App\Services\StorageService')->getFileUrl(Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=181A26&background=8CE0FF&size=120' }}"
                 alt="Profile Picture" 
                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid #8CE0FF; box-shadow: 0 0 20px rgba(140, 224, 255, 0.3);">
            <div class="avatar-badge">✨</div>
        </div>
        <h2 class="profile-name">{{ Auth::user()->name }}</h2>
        <p class="profile-email">{{ Auth::user()->email }}</p>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="showTab('profile-info')">📋 Informasi Akun</button>
        <button class="tab-btn" onclick="showTab('security')">🔒 Keamanan</button>
        <button class="tab-btn" onclick="showTab('danger')">⚠️ Zona Bahaya</button>
    </div>

    <!-- Profile Sections -->
    <div class="profile-sections">
        <!-- Profile Information -->
        <div class="profile-section" id="profile-info-section">
            <div class="section-header">
                <div class="section-icon">📋</div>
                <h3 class="section-title">Informasi Akun</h3>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @include('profile.partials.update-profile-information-form')
            </form>
        </div>

        <!-- Security Section -->
        <div class="profile-section" id="security-section" style="display: none;">
            <div class="section-header">
                <div class="section-icon">🔒</div>
                <h3 class="section-title">Ganti Password</h3>
            </div>
            @include('profile.partials.update-password-form')
        </div>

        <!-- Danger Zone -->
        <div class="profile-section" id="danger-section" style="display: none;">
            <div class="section-header">
                <div class="section-icon">⚠️</div>
                <h3 class="section-title">Hapus Akun</h3>
            </div>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all sections
    document.getElementById('profile-info-section').style.display = 'none';
    document.getElementById('security-section').style.display = 'none';
    document.getElementById('danger-section').style.display = 'none';
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    // Show selected section and activate tab
    if (tabName === 'profile-info') {
        document.getElementById('profile-info-section').style.display = 'block';
        document.querySelectorAll('.tab-btn')[0].classList.add('active');
    } else if (tabName === 'security') {
        document.getElementById('security-section').style.display = 'block';
        document.querySelectorAll('.tab-btn')[1].classList.add('active');
    } else if (tabName === 'danger') {
        document.getElementById('danger-section').style.display = 'block';
        document.querySelectorAll('.tab-btn')[2].classList.add('active');
    }
}
</script>
@endsection