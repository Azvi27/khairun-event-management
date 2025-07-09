@extends('layouts.khairun')

@section('title', 'Edit Birthday Surprise')

@push('styles')
<style>
.edit-container {
    max-width: 700px;
    margin: 0 auto;
}

.edit-header {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 40px;
    border: 2px solid rgba(140, 224, 255, 0.3);
}

.edit-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.edit-subtitle {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.9;
}

.current-info {
    background: rgba(251, 191, 36, 0.1);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 40px;
    border: 1px solid rgba(251, 191, 36, 0.3);
}

.current-info-title {
    color: #fbbf24;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.current-details {
    color: #D3D3D9;
    line-height: 1.6;
}

.form-container {
    background: #181A26;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    border: 1px solid rgba(140, 224, 255, 0.2);
}

.form-group {
    margin-bottom: 30px;
}

.form-label {
    display: block;
    color: #8CE0FF;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.content-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.radio-option {
    background: rgba(52, 54, 70, 0.6);
    border-radius: 15px;
    padding: 20px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.radio-option:hover {
    border-color: rgba(140, 224, 255, 0.5);
    background: rgba(52, 54, 70, 0.8);
}

.radio-option.selected {
    border-color: #8CE0FF;
    background: rgba(140, 224, 255, 0.1);
}

.radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.option-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.option-label {
    color: #D3D3D9;
    font-weight: 600;
    font-size: 1rem;
}

.form-input, .form-textarea {
    width: 100%;
    background: rgba(52, 54, 70, 0.6);
    border: 2px solid rgba(140, 224, 255, 0.2);
    border-radius: 12px;
    padding: 15px;
    color: #D3D3D9;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #8CE0FF;
    background: rgba(52, 54, 70, 0.8);
    box-shadow: 0 0 0 3px rgba(140, 224, 255, 0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-warning {
    background: rgba(239, 68, 68, 0.1);
    border-left: 4px solid #ef4444;
    border-radius: 8px;
    padding: 12px 15px;
    margin-top: 10px;
    color: #D3D3D9;
    font-size: 0.9rem;
}

.error-container {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    color: white;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid rgba(140, 224, 255, 0.2);
    margin-top: 30px;
}

.btn-back {
    background: linear-gradient(135deg, #343646 0%, #181A26 100%);
    color: #D3D3D9;
    padding: 12px 25px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
    color: #181A26;
    transform: translateY(-2px);
}

.btn-submit {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    padding: 15px 30px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
}
</style>
@endpush

@section('content')
<div class="edit-container">
    <!-- Header -->
    <div class="edit-header">
        <h1 class="edit-title">✏️ Edit Surprise</h1>
        <p class="edit-subtitle">Sempurnakan surprise sebelum momen spesial tiba</p>
    </div>

    <!-- Current Info -->
    <div class="current-info">
        <div class="current-info-title">
            <span>⚠️</span>
            <span>Editing Surprise untuk {{ $otherUser->name ?? 'Khairun' }}</span>
        </div>
        <div class="current-details">
            <strong>Saat ini dijadwalkan:</strong> {{ $birthdaySurprise->reveal_at->format('d F Y, H:i') }} WIB<br>
            <strong>Status:</strong> {{ $birthdaySurprise->isRevealed() ? 'Sudah terbuka' : 'Menunggu waktu reveal' }}
        </div>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="error-container">
            <div style="font-weight: 600; margin-bottom: 10px;">❌ Ada yang perlu diperbaiki:</div>
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($errors->all() as $error)
                    <li style="padding: 5px 0;">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="form-container">
        <form method="POST" action="{{ route('birthday-surprises.update', $birthdaySurprise->id) }}" id="editForm">
            @csrf
            @method('PUT')

            <!-- Content Type Selection -->
            <div class="form-group">
                <label class="form-label">📋 Jenis Surprise</label>
                <div class="content-type-grid">
                    <div class="radio-option {{ $birthdaySurprise->content_type == 'message' ? 'selected' : '' }}" data-value="message">
                        <input type="radio" name="content_type" value="message" {{ $birthdaySurprise->content_type == 'message' ? 'checked' : '' }}>
                        <span class="option-icon">💌</span>
                        <div class="option-label">Pesan Heartfelt</div>
                    </div>
                    <div class="radio-option {{ $birthdaySurprise->content_type == 'image' ? 'selected' : '' }}" data-value="image">
                        <input type="radio" name="content_type" value="image" {{ $birthdaySurprise->content_type == 'image' ? 'checked' : '' }}>
                        <span class="option-icon">🖼️</span>
                        <div class="option-label">Gambar Spesial</div>
                    </div>
                    <div class="radio-option {{ $birthdaySurprise->content_type == 'video_link' ? 'selected' : '' }}" data-value="video_link">
                        <input type="radio" name="content_type" value="video_link" {{ $birthdaySurprise->content_type == 'video_link' ? 'checked' : '' }}>
                        <span class="option-icon">🎥</span>
                        <div class="option-label">Video/Link</div>
                    </div>
                </div>
            </div>

            <!-- Content Payload -->
            <div class="form-group">
                <label for="content_payload" class="form-label">✍️ Isi Surprise</label>
                <textarea 
                    id="content_payload" 
                    name="content_payload" 
                    class="form-textarea"
                    placeholder="Edit pesan atau link..."
                >{{ old('content_payload', $birthdaySurprise->content_payload) }}</textarea>
            </div>

            <!-- Reveal Date & Time -->
            <div class="form-group">
                <label for="reveal_at" class="form-label">⏰ Waktu Reveal</label>
                <input 
                    type="datetime-local" 
                    id="reveal_at" 
                    name="reveal_at" 
                    class="form-input"
                    value="{{ old('reveal_at', $birthdaySurprise->reveal_at->format('Y-m-d\TH:i')) }}"
                >
                <div class="form-warning">
                    ⚠️ Hati-hati: Mengubah waktu reveal bisa mempengaruhi countdown yang sudah berjalan
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('birthday-surprises.index') }}" class="btn-back">
                    ⬅️ Kembali
                </a>
                <button type="submit" class="btn-submit">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle radio option selection
    const radioOptions = document.querySelectorAll('.radio-option');
    const contentPayload = document.getElementById('content_payload');
    
    radioOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            radioOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Check the radio input
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Update placeholder based on selection
            const value = this.dataset.value;
            switch(value) {
                case 'message':
                    contentPayload.placeholder = 'Edit pesan yang menyentuh hati...';
                    break;
                case 'image':
                    contentPayload.placeholder = 'Edit URL gambar (contoh: https://example.com/image.jpg)';
                    break;
                case 'video_link':
                    contentPayload.placeholder = 'Edit link video atau URL spesial...';
                    break;
            }
        });
    });
});
</script>
@endsection