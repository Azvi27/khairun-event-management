@extends('layouts.khairun')

@section('title', 'Timeline of Memories - Our Memories')

@push('styles')
<link href="{{ asset('css/memories.css') }}" rel="stylesheet">
@endpush

@section('content')
<h1 class="page-title">Timeline of Memories</h1>
<p class="page-subtitle">Click here — let the memories take you where words once lived.</p>

<!-- Our Journey Section -->
<section class="journey-section">
    <h2 class="section-title">Our Journey</h2>
    <p class="journey-subtitle">Swipe through our beautiful memories timeline</p>
    
    @if($memories->count() > 0)
        <div class="carousel">
            <button class="carousel-nav carousel-prev" onclick="slideCarousel(-1)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            
            <div class="carousel-inner" id="carouselInner">
                @foreach($memories as $memory)
                    <div class="carousel-item" onclick="window.location.href='{{ route('memories.show', $memory) }}'">
                        <div class="carousel-image-container">
                            @if($memory->image_path)
                                <img src="{{ app('App\Services\StorageService')->getFileUrl($memory->image_path) }}"
                                     alt="Memory" 
                                     class="memory-image">
                                <div class="carousel-image-overlay">
                                    <div class="carousel-overlay-icon">📸</div>
                                </div>
                            @else
                                <div class="carousel-text-preview">
                                    <div class="carousel-text-icon">💭</div>
                                    <div class="carousel-text-content">
                                        "{{ Str::limit($memory->description, 100) }}"
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="carousel-content">
                            <div class="carousel-date">
                                <span class="date-icon">📅</span>
                                <span>{{ $memory->memory_date->format('d M Y') }}</span>
                            </div>
                            
                            <div class="carousel-description">
                                {{ Str::limit($memory->description, 80) }}
                            </div>
                            
                            <div class="carousel-actions">
                                <a href="{{ route('memories.show', $memory) }}" class="carousel-btn btn-view">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('memories.edit', $memory) }}" class="carousel-btn btn-edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('memories.destroy', $memory) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="carousel-btn btn-delete" onclick="return confirm('Delete this memory?')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav carousel-next" onclick="slideCarousel(1)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📸</div>
            <div class="empty-state-text">
                <h3>No memories yet!</h3>
                <p>Start creating your beautiful journey together</p>
                <a href="{{ route('memories.create') }}" class="empty-state-btn">
                    ➕ Add First Memory
                </a>
            </div>
        </div>
    @endif
</section>

<!-- Add New Memories Section -->
<section class="add-memory-section">
    <h2 class="section-title">Add New Memories</h2>
    <form action="{{ route('memories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="memory_date" class="form-label">Date</label>
                <input type="date" 
                       id="memory_date" 
                       name="memory_date" 
                       class="form-input" 
                       value="{{ old('memory_date') }}"
                       required>
                @error('memory_date')
                    <span style="color: #ff6b6b; font-size: 0.8em;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="spotify_track_id" class="form-label">Mood/Song (Optional)</label>
                <input type="text" 
                       id="spotify_track_id" 
                       name="spotify_track_id" 
                       class="form-input" 
                       placeholder="Spotify Track ID"
                       value="{{ old('spotify_track_id') }}">
                @error('spotify_track_id')
                    <span style="color: #ff6b6b; font-size: 0.8em;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea" 
                          placeholder="Write your stories here..."
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <span style="color: #ff6b6b; font-size: 0.8em;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="image" class="form-label">Upload Image</label>
                <label for="image" class="form-upload">
                    <span id="upload-text">Click to upload</span>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <img id="preview" class="upload-preview" style="display: none;">
                </label>
                @error('image')
                    <span style="color: #ff6b6b; font-size: 0.8em;">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="add-memory-btn-container">
            <button type="submit" class="add-memory-btn">Add Memories</button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
// Enhanced Carousel functionality
let currentSlide = 0;
const slideWidth = 370; // 350px width + 20px gap

function slideCarousel(direction) {
    const carousel = document.getElementById('carouselInner');
    if (!carousel) return;
    
    const items = carousel.querySelectorAll('.carousel-item');
    const maxSlides = items.length;
    const containerWidth = carousel.parentElement.offsetWidth;
    const visibleSlides = Math.floor(containerWidth / slideWidth);
    const maxPosition = Math.max(0, maxSlides - visibleSlides);
    
    currentSlide += direction;
    
    // Boundary checks
    if (currentSlide < 0) {
        currentSlide = 0;
    } else if (currentSlide > maxPosition) {
        currentSlide = maxPosition;
    }
    
    const translateX = -currentSlide * slideWidth;
    carousel.style.transform = `translateX(${translateX}px)`;
    
    // Update navigation button states
    updateNavigationButtons(currentSlide, maxPosition);
}

function updateNavigationButtons(current, max) {
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    if (prevBtn && nextBtn) {
        prevBtn.style.opacity = current === 0 ? '0.5' : '1';
        nextBtn.style.opacity = current === max ? '0.5' : '1';
        
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current === max;
    }
}

// Auto-play carousel (optional)
let autoPlayInterval;

function startAutoPlay() {
    const carousel = document.getElementById('carouselInner');
    if (!carousel) return;
    
    autoPlayInterval = setInterval(() => {
        const items = carousel.querySelectorAll('.carousel-item');
        const containerWidth = carousel.parentElement.offsetWidth;
        const visibleSlides = Math.floor(containerWidth / slideWidth);
        const maxPosition = Math.max(0, items.length - visibleSlides);
        
        if (currentSlide >= maxPosition) {
            currentSlide = 0;
        } else {
            currentSlide++;
        }
        
        const translateX = -currentSlide * slideWidth;
        carousel.style.transform = `translateX(${translateX}px)`;
        updateNavigationButtons(currentSlide, maxPosition);
    }, 4000); // Auto-slide every 4 seconds
}

function stopAutoPlay() {
    clearInterval(autoPlayInterval);
}

// Initialize carousel when page loads
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    const carouselInner = document.getElementById('carouselInner');
    
    if (carousel && carouselInner) {
        // Initialize button states
        const items = carouselInner.querySelectorAll('.carousel-item');
        const containerWidth = carousel.offsetWidth;
        const visibleSlides = Math.floor(containerWidth / slideWidth);
        const maxPosition = Math.max(0, items.length - visibleSlides);
        
        updateNavigationButtons(0, maxPosition);
        
        // Start auto-play if there are enough items
        if (items.length > visibleSlides) {
            startAutoPlay();
            
            // Pause auto-play on hover
            carousel.addEventListener('mouseenter', stopAutoPlay);
            carousel.addEventListener('mouseleave', startAutoPlay);
        }
        
        // Handle window resize
        window.addEventListener('resize', () => {
            const newContainerWidth = carousel.offsetWidth;
            const newVisibleSlides = Math.floor(newContainerWidth / slideWidth);
            const newMaxPosition = Math.max(0, items.length - newVisibleSlides);
            
            if (currentSlide > newMaxPosition) {
                currentSlide = newMaxPosition;
                const translateX = -currentSlide * slideWidth;
                carouselInner.style.transform = `translateX(${translateX}px)`;
            }
            
            updateNavigationButtons(currentSlide, newMaxPosition);
        });
    }
});

// Image preview function
function previewImage(input) {
    const preview = document.getElementById('preview');
    const uploadText = document.getElementById('upload-text');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            uploadText.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        slideCarousel(-1);
    } else if (e.key === 'ArrowRight') {
        slideCarousel(1);
    }
});
</script>
@endpush