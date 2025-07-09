@extends('layouts.khairun')

@section('title', 'Tambah Kenangan Baru')

@push('styles')
<style>
    /* Add Memory Section Styles */
    .add-memory-section {
        background: #343646;
        border-radius: 2.3vh;
        padding: 2.2vh 3.1vw;
        margin-top: 5vh;
    }

    .section-title {
        color: #D3D3D9;
        font-size: 1.56vw;
        font-family: 'DM Serif Text', serif;
        margin-bottom: 2vh;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2vh 2vw;
        margin-top: 2vh;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #D3D3D9;
        font-size: 1.04vw;
        font-weight: 500;
        margin-bottom: 1vh;
    }

    .form-input, .form-textarea {
        background: transparent;
        border-radius: 1.25vh;
        border: 0.09vh solid #D3D3D9;
        padding: 1.5vh 1vw;
        color: #D3D3D9;
        font-size: 1.04vw;
        font-family: 'Poppins', sans-serif;
    }
    
    .form-input::placeholder, .form-textarea::placeholder {
        color: #8F8C8C;
    }

    .form-textarea {
        height: 18.5vh;
        resize: vertical;
    }

    .form-upload {
        height: 20vh;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-style: dashed;
        border: 2px dashed #D3D3D9;
        border-radius: 1.25vh;
        color: #D3D3D9;
    }

    .add-memory-btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 3vh;
    }

    .add-memory-btn, .cancel-btn {
        border-radius: 4.2vh;
        padding: 1.5vh 5vw;
        font-size: 1.01vw;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .add-memory-btn {
        background: #D3D3D9;
        color: #181A26;
    }

    .cancel-btn {
        background: #8F8C8C;
        color: #D3D3D9;
    }

    .add-memory-btn:hover {
        background: #bfbfbf;
    }

    .cancel-btn:hover {
        background: #7a7878;
    }

    /* Responsive Design */
    @media (max-width: 1440px) {
        .form-label {
            font-size: 16px;
        }

        .form-input, .form-textarea {
            font-size: 16px;
        }

        .section-title {
            font-size: 32px;
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .add-memory-section {
            padding: 20px;
        }

        .section-title {
            font-size: 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <h1 class="page-title">Timeline of Memories</h1>
    <p class="page-subtitle">Add a new chapter to our story.</p>

    <!-- Add New Memories Section -->
    <section class="add-memory-section">
        <h2 class="section-title">✨ Add New Memories</h2>
        <form action="{{ route('memories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="memory_date" class="form-label">📅 Date</label>
                    <input type="date" 
                           id="memory_date" 
                           name="memory_date" 
                           class="form-input" 
                           value="{{ old('memory_date') }}"
                           required>
                    @error('memory_date')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="spotify_track" class="form-label">🎵 Add Music (Optional)</label>
                    <input type="text" 
                           id="spotify_search" 
                           class="form-input" 
                           placeholder="Search for a song...">
                    <input type="hidden" 
                           name="spotify_track_id" 
                           id="spotify_track_id">
                </div>

                <div class="form-group full-width">
                    <label for="description" class="form-label">📝 Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-textarea" 
                              placeholder="Write your stories here..."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="image" class="form-label">📸 Upload Image (Optional)</label>
                    <label for="image" class="form-upload">
                        <span>Click or drag image here to upload</span>
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image"
                           accept="image/*"
                           style="display: none;">
                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="add-memory-btn-container">
                <a href="{{ route('dashboard') }}" class="cancel-btn">
                    ← Cancel
                </a>
                <button type="submit" class="add-memory-btn">
                    💾 Save Memory
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('image').addEventListener('change', function(e) {
        const fileInput = e.target;
        const uploadLabel = document.querySelector('.form-upload');
        
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                uploadLabel.style.backgroundImage = `url(${e.target.result})`;
                uploadLabel.style.backgroundSize = 'cover';
                uploadLabel.style.backgroundPosition = 'center';
                uploadLabel.innerHTML = '';
            }
            
            reader.readAsDataURL(fileInput.files[0]);
        }
    });
</script>
@endpush