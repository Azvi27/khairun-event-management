@extends('layouts.khairun')

@section('title', 'Memory Detail')

@push('styles')
<style>
.memory-detail-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.memory-header {
    background: linear-gradient(135deg, #181A26 0%, #262840 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 30px;
    border: 2px solid rgba(140, 224, 255, 0.3);
}

.memory-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.memory-subtitle {
    color: #D3D3D9;
    font-size: 1.1rem;
    opacity: 0.9;
}

.memory-card {
    background: #181A26;
    border-radius: 20px;
    padding: 0;
    border: 2px solid rgba(140, 224, 255, 0.2);
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.memory-card::before {
    content: '';
    display: block;
    height: 5px;
    background: linear-gradient(90deg, #8CE0FF, #FFD93D, #FF6B6B);
}

.memory-image-section {
    position: relative;
    max-height: 500px;
    overflow: hidden;
}

.memory-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.memory-hero-image:hover {
    transform: scale(1.02);
}

.memory-no-image {
    height: 300px;
    background: rgba(52, 54, 70, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D3D3D9;
    font-size: 1.2rem;
    font-style: italic;
}

.memory-content {
    padding: 40px;
}

.memory-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.memory-date-badge {
    background: rgba(140, 224, 255, 0.2);
    border: 1px solid #8CE0FF;
    padding: 10px 20px;
    border-radius: 25px;
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.memory-music-badge {
    background: rgba(255, 217, 61, 0.2);
    border: 1px solid #FFD93D;
    padding: 10px 20px;
    border-radius: 25px;
    color: #FFD93D;
    display: flex;
    align-items: center;
    gap: 8px;
}

.memory-story {
    background: rgba(211, 211, 217, 0.1);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    border-left: 4px solid #8CE0FF;
}

.memory-story-title {
    color: #8CE0FF;
    font-family: 'DM Serif Text', serif;
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.memory-story-content {
    color: #D3D3D9;
    font-size: 1.1rem;
    line-height: 1.8;
    white-space: pre-wrap;
}

.memory-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.action-button {
    padding: 12px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    border: none;
    font-size: 1rem;
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.btn-edit {
    background: linear-gradient(135deg, #FFD93D 0%, #fbbf24 100%);
    color: #181A26;
}

.btn-delete {
    background: linear-gradient(135deg, #FF6B6B 0%, #ef4444 100%);
    color: white;
}

.btn-back {
    background: linear-gradient(135deg, #6D718D 0%, #4b5563 100%);
    color: white;
}

.delete-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.delete-modal.active {
    display: flex;
}

.delete-modal-content {
    background: #181A26;
    border-radius: 20px;
    padding: 40px;
    max-width: 500px;
    text-align: center;
    border: 2px solid rgba(239, 68, 68, 0.3);
}

.delete-modal-title {
    color: #FF6B6B;
    font-size: 1.5rem;
    margin-bottom: 20px;
    font-family: 'DM Serif Text', serif;
}

.delete-modal-text {
    color: #D3D3D9;
    margin-bottom: 30px;
    line-height: 1.6;
}

.delete-modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

</style>
@endpush

@section('content')
<div class="memory-detail-container">
    <!-- Header -->
    <div class="memory-header">
        <h1 class="memory-title">💝 Memory Detail</h1>
        <p class="memory-subtitle">Relive this precious moment</p>
    </div>

    <div class="memory-card">
        <!-- Image Section -->
        @if($memory->image_path && app('App\Services\StorageService')->fileExists($memory->image_path))
            <div class="memory-image-section">
                <img src="{{ app('App\Services\StorageService')->getFileUrl($memory->image_path) }}" 
                     alt="Memory from {{ $memory->memory_date->format('F d, Y') }}" 
                     class="memory-hero-image">
            </div>
        @else
            <div class="memory-no-image">
                @if($memory->image_path)
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">🖼️</div>
                        <div>Image not found</div>
                        <div style="font-size: 0.9rem; opacity: 0.7;">{{ $memory->image_path }}</div>
                    </div>
                @else
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">💭</div>
                        <div>Text-only memory</div>
                    </div>
                @endif
            </div>
        @endif
        
        <!-- Content -->
        <div class="memory-content">
            <div class="memory-meta">
                <div class="memory-date-badge">
                    <span>📅</span>
                    <span>{{ $memory->memory_date->format('F d, Y') }}</span>
                </div>
                
                @if($memory->spotify_track_id)
                    <div class="memory-music-badge">
                        <span>🎵</span>
                        <span>Has background music</span>
                    </div>
                @endif
            </div>
            
            <div class="memory-story">
                <h2 class="memory-story-title">The Story</h2>
                <p class="memory-story-content">{{ $memory->description }}</p>
            </div>
            
            <div class="memory-actions">
                <a href="{{ route('memories.edit', $memory) }}" class="action-button btn-edit">
                    ✏️ Edit Memory
                </a>
                <button onclick="showDeleteModal()" class="action-button btn-delete">
                    🗑️ Delete Memory
                </button>
                <a href="{{ route('memories.index') }}" class="action-button btn-back">
                    ← Back to Timeline
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <h3 class="delete-modal-title">⚠️ Delete Memory?</h3>
        <p class="delete-modal-text">
            Are you sure you want to delete this memory?<br>
            This action cannot be undone.
        </p>
        <div class="delete-modal-actions">
            <button onclick="hideDeleteModal()" class="action-button btn-back">
                Cancel
            </button>
            <form action="{{ route('memories.destroy', $memory) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-button btn-delete">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function showDeleteModal() {
    document.getElementById('deleteModal').classList.add('active');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
@endsection