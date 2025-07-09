@extends('layouts.khairun')

@section('title', 'Buat Birthday Surprise')

@push('styles')
<style>
.create-container {
    max-width: 700px;
    margin: 0 auto;
}

.create-header {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 40px;
    border: 2px solid rgba(140, 224, 255, 0.3);
}

.create-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.create-subtitle {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.9;
}

.recipient-info {
    background: rgba(140, 224, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 40px;
    text-align: center;
    border: 1px solid rgba(140, 224, 255, 0.3);
}

.recipient-name {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 1.5rem;
    margin: 0;
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

.form-tip {
    background: rgba(251, 191, 36, 0.1);
    border-left: 4px solid #fbbf24;
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

.error-title {
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.error-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.error-list li {
    padding: 5px 0;
    padding-left: 20px;
    position: relative;
}

.error-list li:before {
    content: "•";
    color: rgba(255, 255, 255, 0.8);
    position: absolute;
    left: 0;
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
    background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
    color: #181A26;
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
    box-shadow: 0 8px 25px rgba(140, 224, 255, 0.4);
}
</style>
@endpush

@section('content')
<div class="create-container">
    <!-- Header -->
    <div class="create-header">
        <h1 class="create-title">🎁 Buat Surprise Spesial</h1>
        <p class="create-subtitle">Ciptakan momen magis yang akan diingat selamanya</p>
    </div>

    <!-- Recipient Info -->
    <div class="recipient-info">
        <h3 class="recipient-name">✨ Untuk {{ $otherUser->name ?? 'Khairun' }} ✨</h3>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="error-container">
            <div class="error-title">❌ Oops! Ada yang perlu diperbaiki:</div>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <div class="form-container">
        <form method="POST" action="{{ route('birthday-surprises.store') }}" id="surpriseForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="receiver_user_id" value="{{ $otherUser->id }}">

            <!-- Content Type Selection -->
            <div class="form-group">
                <label class="form-label">📋 Pilih Jenis Surprise</label>
                <div class="content-type-grid">
                    <div class="radio-option" data-value="message">
                        <input type="radio" name="content_type" value="message" id="message">
                        <span class="option-icon">💌</span>
                        <div class="option-label">Pesan Heartfelt</div>
                    </div>
                    <div class="radio-option" data-value="image">
                        <input type="radio" name="content_type" value="image" id="image">
                        <span class="option-icon">🖼️</span>
                        <div class="option-label">Gambar Spesial</div>
                    </div>
                    <div class="radio-option" data-value="video_link">
                        <input type="radio" name="content_type" value="video_link" id="video_link">
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
                    placeholder="Tulis pesan yang menyentuh hati, atau masukkan link gambar/video spesial..."
                    value="{{ old('content_payload') }}"
                ></textarea>
            </div>

            <!-- Isi Surprise -->
            <div class="form-group">
                <label class="form-label">🖼️ Upload Gambar Spesial (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="form-input">
                <div class="form-tip">
                    💡 Tambahkan gambar untuk membuat surprise lebih istimewa! (Opsional)
                </div>
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reveal Date & Time -->
            <div class="form-group">
                <label for="reveal_at" class="form-label">⏰ Kapan Surprise Dibuka?</label>
                <input 
                    type="datetime-local" 
                    id="reveal_at" 
                    name="reveal_at" 
                    class="form-input"
                    value="{{ old('reveal_at') }}"
                >
                <div class="form-tip">
                    💡 Pro tip: Jadwalkan 1-2 hari sebelum momen spesial untuk membangun excitement!
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('birthday-surprises.index') }}" class="btn-back">
                    ⬅️ Kembali
                </a>
                <button type="submit" class="btn-submit">
                    🎁 Jadwalkan Surprise
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
                    contentPayload.placeholder = 'Tulis pesan yang menyentuh hati...';
                    break;
                case 'image':
                    contentPayload.placeholder = 'Masukkan URL gambar (contoh: https://example.com/image.jpg)';
                    break;
                case 'video_link':
                    contentPayload.placeholder = 'Masukkan link video YouTube, Instagram, atau link spesial lainnya...';
                    break;
            }
        });
    });
    
    // Set initial selected state
    const checkedRadio = document.querySelector('input[name="content_type"]:checked');
    if (checkedRadio) {
        const option = checkedRadio.closest('.radio-option');
        option.classList.add('selected');
    }
});
</script>
@endsection