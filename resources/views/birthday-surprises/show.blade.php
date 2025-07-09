@extends('layouts.khairun')

@section('title', 'Detail Birthday Surprise')

@push('styles')
<style>
.surprise-container {
    max-width: 800px;
    margin: 0 auto;
}

.surprise-header {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 30px;
    border: 2px solid rgba(140, 224, 255, 0.3);
}

.surprise-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.surprise-subtitle {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.9;
}

.surprise-content {
    background: #181A26;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    border: 1px solid rgba(140, 224, 255, 0.2);
}

.content-type-badge {
    background: #8CE0FF;
    color: #181A26;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 20px;
    display: inline-block;
}

.content-payload {
    background: rgba(52, 54, 70, 0.6);
    border-radius: 15px;
    padding: 25px;
    margin: 20px 0;
    border-left: 4px solid #8CE0FF;
}

.message-content {
    color: #D3D3D9;
    font-size: 1.1rem;
    line-height: 1.6;
    font-style: italic;
}

.image-content {
    max-width: 100%;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.video-link {
    color: #8CE0FF;
    text-decoration: none;
    font-size: 1.1rem;
    word-break: break-all;
    display: block;
    padding: 15px;
    background: rgba(140, 224, 255, 0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.video-link:hover {
    background: rgba(140, 224, 255, 0.2);
    transform: translateY(-2px);
}

.reveal-info {
    background: rgba(140, 224, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid rgba(140, 224, 255, 0.3);
}

.back-button {
    background: linear-gradient(135deg, #343646 0%, #181A26 100%);
    color: #D3D3D9;
    padding: 12px 30px;
    border-radius: 15px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}

.back-button:hover {
    background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
    color: #181A26;
    transform: translateY(-2px);
}
</style>
@endpush

@section('content')
<div class="surprise-container">
    <!-- Header -->
    <div class="surprise-header">
        <h1 class="surprise-title">🎁 Sudah Terkirim!</h1>
        <p class="surprise-subtitle">Moment spesial yang telah menunggu untuk dibagikan</p>
    </div>

    <!-- Content -->
    <div class="surprise-content">
        <h2 style="color: #8CE0FF; font-family: 'DM Serif Text', serif; font-size: 1.8rem; margin-bottom: 20px;">
            Surprise dari {{ $birthdaySurprise->sender->name ?? 'Seseorang Spesial' }} 💝
        </h2>

        <!-- Content Type Badge -->
        <div class="content-type-badge">
            <span class="mr-2">{{ $birthdaySurprise->getContentIcon() }}</span>
            @if($birthdaySurprise->content_type == 'message')
                Pesan Spesial
            @elseif($birthdaySurprise->content_type == 'image')
                Gambar Kenangan
            @elseif($birthdaySurprise->content_type == 'video_link')
                Video/Link Spesial
            @endif
        </div>

        <!-- Content Payload -->
        <div class="content-payload">
            @if($birthdaySurprise->content_type == 'message')
                <div class="message-content">
                    "{{ $birthdaySurprise->content_payload }}"
                </div>
            @elseif($birthdaySurprise->content_type == 'image')
                @if($birthdaySurprise->content)
                    <img src="{{ Storage::url($birthdaySurprise->content) }}" alt="Gambar Spesial" class="image-content">
                @endif
                @if($birthdaySurprise->content_payload)
                    <div class="message-content" style="margin-top: 15px;">
                        "{{ $birthdaySurprise->content_payload }}"
                    </div>
                @endif
            @elseif($birthdaySurprise->content_type == 'video_link')
                <a href="{{ $birthdaySurprise->content_payload }}" target="_blank" class="video-link">
                    🎥 {{ $birthdaySurprise->content_payload }}
                </a>
            @endif
        </div>

        <!-- Reveal Info -->
        <div class="reveal-info">
            <div style="color: #D3D3D9; margin-bottom: 5px;">
                <strong>📅 Dijadwalkan untuk dibuka:</strong>
            </div>
            <div style="color: #8CE0FF; font-size: 1.1rem; font-weight: 600;">
                {{ $birthdaySurprise->reveal_at->format('d F Y, H:i') }} WIB
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div style="text-align: center;">
        <a href="{{ route('birthday-surprises.index') }}" class="back-button">
            ⬅️ Kembali ke Daftar Surprise
        </a>
    </div>
</div>
@endsection