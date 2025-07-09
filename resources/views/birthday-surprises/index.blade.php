@extends('layouts.khairun')

@section('title', 'Daftar Birthday Surprise')

@push('styles')
<style>
.surprise-page-header {
    text-align: center;
    margin-bottom: 40px;
}

.surprise-page-title {
    color: #181A26;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.surprise-page-subtitle {
    color: #666;
    font-size: 1.1rem;
}

.create-surprise-btn {
    background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
    color: #181A26;
    padding: 15px 30px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-block;
    margin-bottom: 40px;
}

.create-surprise-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(140, 224, 255, 0.4);
}

.surprise-section {
    margin-bottom: 50px;
}

.section-header {
    background: #181A26;
    border-radius: 15px;
    padding: 20px 30px;
    margin-bottom: 25px;
    border: 2px solid rgba(140, 224, 255, 0.3);
}

.section-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 1.8rem;
    margin: 0;
}

.surprise-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.surprise-card {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(140, 224, 255, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.surprise-card:hover {
    transform: translateY(-5px);
    border-color: rgba(140, 224, 255, 0.5);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.surprise-card.received {
    border-left: 4px solid #8CE0FF;
}

.surprise-card.sent {
    border-left: 4px solid #fbbf24;
}

.surprise-icon {
    font-size: 2rem;
    margin-bottom: 15px;
    display: block;
}

.surprise-date {
    color: #D3D3D9;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.surprise-status {
    font-weight: 600;
    margin-bottom: 20px;
    padding: 8px 15px;
    border-radius: 10px;
    display: inline-block;
}

.status-revealed {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.status-waiting {
    background: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
}

.surprise-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.action-btn {
    padding: 8px 15px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-view {
    background: #8CE0FF;
    color: #181A26;
}

.btn-view:hover {
    background: #6bd4ff;
    transform: translateY(-2px);
}

.btn-edit {
    background: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
}

.btn-edit:hover {
    background: rgba(251, 191, 36, 0.3);
}

.btn-delete {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: none;
    cursor: pointer;
}

.btn-delete:hover {
    background: rgba(239, 68, 68, 0.3);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: rgba(52, 54, 70, 0.3);
    border-radius: 20px;
    border: 2px dashed rgba(140, 224, 255, 0.3);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.6;
}

.empty-text {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.8;
}
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="surprise-page-header">
    <h1 class="surprise-page-title">🎁 Birthday Surprise</h1>
    <p class="surprise-page-subtitle">Kelola momen spesial dan kejutan yang tak terlupakan</p>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; text-align: center; font-weight: 600;">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; text-align: center; font-weight: 600;">
        ❌ {{ session('error') }}
    </div>
@endif

<!-- Create Button -->
<div style="text-align: center; margin-bottom: 50px;">
    <a href="{{ route('birthday-surprises.create') }}" class="create-surprise-btn">
        🎁 Buat Surprise Baru
    </a>
</div>

<!-- Received Surprises -->
<div class="surprise-section">
    <div class="section-header">
        <h2 class="section-title">💝 Surprise untuk Kamu</h2>
    </div>
    
    @if($receivedSurprises->count() > 0)
        <div class="surprise-grid">
            @foreach($receivedSurprises as $surprise)
                <div class="surprise-card received">
                    <span class="surprise-icon">{{ $surprise->getContentIcon() }}</span>
                    <div class="surprise-date">
                        📅 {{ $surprise->reveal_at->format('d F Y, H:i') }}
                    </div>
                    <div class="surprise-status {{ $surprise->isRevealed() ? 'status-revealed' : 'status-waiting' }}">
                        @if($surprise->isRevealed())
                            ✅ Sudah terkirim!
                        @else  
                            ⏰ {{ $surprise->countdown }}
                        @endif
                    </div>
                    
                    @if($surprise->isRevealed() || $surprise->canBeRevealed())
                        <div class="surprise-actions">
                            <a href="{{ route('birthday-surprises.show', $surprise->id) }}" class="action-btn btn-view">
                                👁️
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🎁</div>
            <p class="empty-text">Belum ada surprise untukmu</p>
        </div>
    @endif
</div>

<!-- Sent Surprises -->
<div class="surprise-section">
    <div class="section-header">
        <h2 class="section-title">📤 Surprise yang Kamu Kirim</h2>
    </div>
    
    @if($sentSurprises->count() > 0)
        <div class="surprise-grid">
            @foreach($sentSurprises as $surprise)
                <div class="surprise-card sent">
                    <span class="surprise-icon">{{ $surprise->getContentIcon() }}</span>
                    <div class="surprise-date">
                        📅 {{ $surprise->reveal_at->format('d F Y, H:i') }}
                    </div>
                    <div class="surprise-status {{ $surprise->isRevealed() ? 'status-revealed' : 'status-waiting' }}">
                        @if($surprise->isRevealed())
                            ✅ Sudah terkirim!
                        @else  
                            ⏰ {{ $surprise->countdown }}
                        @endif
                    </div>
                    
                    <div class="surprise-actions">
                        <a href="{{ route('birthday-surprises.show', $surprise->id) }}" class="action-btn btn-view">
                            👁️
                        </a>
                        @if(!$surprise->isRevealed())
                            <a href="{{ route('birthday-surprises.edit', $surprise->id) }}" class="action-btn btn-edit">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('birthday-surprises.destroy', $surprise->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete" onclick="return confirm('Hapus surprise ini?')">
                                    🗑️
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">💝</div>
            <p class="empty-text">Belum ada surprise yang kamu kirim</p>
        </div>
    @endif
</div>
@endsection