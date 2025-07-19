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
        <!-- 🚀 NEW: Timeline Navigation Bar -->
        <div class="timeline-nav">
            <div class="timeline-header">
                <h3 class="timeline-title">📅 Timeline Navigation</h3>
                <div class="timeline-controls">
                    <button class="timeline-btn" id="prevBtn">←</button>
                    <button class="timeline-btn" id="nextBtn">→</button>
                </div>
            </div>
            
            <!-- Timeline Bar with Individual Memory Points -->
            <div class="timeline-bar-container">
                <div class="timeline-bar" id="timelineBar">
                    @foreach($memories as $memory)
                        <div class="timeline-memory-point" 
                             data-memory-id="{{ $memory->id }}" 
                             data-date="{{ $memory->memory_date->format('Y-m-d') }}"
                             title="{{ $memory->memory_date->format('F d, Y') }} - {{ Str::limit($memory->description, 50) }}"
                             onclick="viewMemory({{ $memory->id }})">
                            
                            <div class="memory-point">
                                <div class="point-marker"></div>
                                <div class="point-date">
                                    <div class="point-day">{{ $memory->memory_date->format('d') }}</div>
                                    <div class="point-month">{{ $memory->memory_date->format('M') }}</div>
                                </div>
                            </div>
                            
                            <div class="memory-preview-tooltip">
                                <div class="tooltip-content">
                                    @if($memory->image_path)
                                        <div class="tooltip-image">
                                            <img src="{{ asset('storage/' . $memory->image_path) }}" alt="Memory preview">
                                        </div>
                                    @else
                                        <div class="tooltip-text-icon">📝</div>
                                    @endif
                                    <div class="tooltip-description">
                                        {{ Str::limit($memory->description, 60) }}
                                    </div>
                                    <div class="tooltip-date">
                                        {{ $memory->memory_date->format('F d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 🚀 NEW: Horizontal Memories Scroll Container -->
        <div class="memories-timeline-container">
            <div class="memories-horizontal-scroll" id="memoriesScroll">
                @foreach($memories as $memory)
                    <div class="memory-timeline-card" 
                         data-date="{{ $memory->memory_date->format('Y-m-d') }}" 
                         data-month="{{ $memory->memory_date->format('Y-m') }}"
                         data-description="{{ strtolower($memory->description) }}">
                        
                        <!-- Date Badge -->
                        <div class="memory-date-badge">
                            <div class="date-day">{{ $memory->memory_date->format('d') }}</div>
                            <div class="date-month">{{ $memory->memory_date->format('M') }}</div>
                            <div class="date-year">{{ $memory->memory_date->format('Y') }}</div>
                        </div>

                        <!-- Memory Card Content -->
                        <div class="memory-card-content">
                            @if($memory->image_path)
                                <div class="memory-image-container">
                                    <img src="{{ asset('storage/' . $memory->image_path) }}" 
                                         alt="Memory from {{ $memory->memory_date->format('F d, Y') }}" 
                                         class="memory-image"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.parentElement.querySelector('.image-fallback').style.display='block';"
                                         onclick="openLightbox('{{ asset('storage/' . $memory->image_path) }}', '{{ $memory->description }}')">
                                    
                                    <!-- Fallback for missing images -->
                                    <div class="image-fallback" style="display: none; text-align: center; padding: 20px; background: #343646; border-radius: 8px; border: 2px dashed #8CE0FF;">
                                        <div class="fallback-icon" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;">📷</div>
                                        <div class="fallback-text" style="font-size: 0.8rem; color: #D3D3D9;">Image not available</div>
                                    </div>
                                    
                                    <div class="memory-image-overlay">
                                        <div class="overlay-icon">🔍</div>
                                    </div>
                                </div>
                            @else
                                <div class="memory-text-preview">
                                    <div class="text-icon">📝</div>
                                    <div class="text-content">
                                        {{ Str::limit($memory->description, 60) }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Memory Info -->
                            <div class="memory-info">
                                <div class="memory-title">
                                    {{ Str::limit($memory->description, 50) }}
                                </div>
                                
                                @if($memory->spotify_track_id)
                                    <div class="memory-music">
                                        <span class="music-icon">🎵</span>
                                        <span class="music-text">Soundtrack</span>
                                    </div>
                                @endif
                                
                                <!-- Actions -->
                                <div class="memory-actions">
                                    <a href="{{ route('memories.show', $memory) }}" class="action-btn btn-view" title="View">👁️</a>
                                    <a href="{{ route('memories.edit', $memory) }}" class="action-btn btn-edit" title="Edit">✏️</a>
                                    <form method="POST" action="{{ route('memories.destroy', $memory) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" title="Delete"
                                                onclick="return confirm('Delete this memory?')">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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

<!-- Lightbox Modal -->
<div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img id="lightboxImage" class="lightbox-image" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption"></div>
        <div class="lightbox-nav">
            <button class="lightbox-prev" onclick="navigateLightbox(-1)">‹</button>
            <button class="lightbox-next" onclick="navigateLightbox(1)">›</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Enhanced Memories functionality
let allMemories = [];
let filteredMemories = [];
let currentLightboxIndex = 0;
let lightboxImages = [];

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMemories();
    setupSearchAndFilter();
    setupLightbox();
    setupTimelineNavigation();
});

// Initialize memories data
function initializeMemories() {
    const memoryCards = document.querySelectorAll('.memory-card');
    allMemories = Array.from(memoryCards);
    filteredMemories = [...allMemories];
    
    // Collect images for lightbox navigation
    lightboxImages = [];
    allMemories.forEach((card, index) => {
        const img = card.querySelector('.memory-image');
        if (img) {
            lightboxImages.push({
                src: img.src,
                caption: card.querySelector('.memory-description').textContent.trim(),
                index: index
            });
        }
    });
}

// Setup search and filter functionality
function setupSearchAndFilter() {
    const searchInput = document.getElementById('searchMemories');
    const sortSelect = document.getElementById('sortMemories');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterMemories();
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortMemories();
        });
    }
}

// Filter memories based on search
function filterMemories() {
    const searchTerm = document.getElementById('searchMemories').value.toLowerCase();
    
    filteredMemories = allMemories.filter(card => {
        const description = card.dataset.description;
        const date = card.dataset.date;
        return description.includes(searchTerm) || date.includes(searchTerm);
    });
    
    displayFilteredMemories();
}

// Sort memories
function sortMemories() {
    const sortValue = document.getElementById('sortMemories').value;
    
    filteredMemories.sort((a, b) => {
        switch(sortValue) {
            case 'newest':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'oldest':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'alphabetical':
                return a.dataset.description.localeCompare(b.dataset.description);
            default:
                return 0;
        }
    });
    
    displayFilteredMemories();
}

// Display filtered memories
function displayFilteredMemories() {
    const grid = document.getElementById('memoriesGrid');
    
    // Hide all cards first
    allMemories.forEach(card => {
        card.style.display = 'none';
    });
    
    // Show filtered cards
    filteredMemories.forEach((card, index) => {
        card.style.display = 'block';
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });
    
    // Show no results message if needed
    if (filteredMemories.length === 0) {
        showNoResultsMessage();
    } else {
        hideNoResultsMessage();
    }
}

// Show no results message
function showNoResultsMessage() {
    let noResultsDiv = document.getElementById('noResults');
    if (!noResultsDiv) {
        noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'noResults';
        noResultsDiv.className = 'no-results';
        noResultsDiv.innerHTML = `
            <div class="no-results-icon">🔍</div>
            <h3>No memories found</h3>
            <p>Try adjusting your search terms or filters</p>
        `;
        document.getElementById('memoriesGrid').appendChild(noResultsDiv);
    }
    noResultsDiv.style.display = 'block';
}

// Hide no results message
function hideNoResultsMessage() {
    const noResultsDiv = document.getElementById('noResults');
    if (noResultsDiv) {
        noResultsDiv.style.display = 'none';
    }
}

// Lightbox functionality
function setupLightbox() {
    // Close lightbox with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            navigateLightbox(-1);
        } else if (e.key === 'ArrowRight') {
            navigateLightbox(1);
        }
    });
}

// Open lightbox
function openLightbox(imageSrc, caption) {
    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    const captionElement = document.getElementById('lightboxCaption');
    
    // Find current image index
    currentLightboxIndex = lightboxImages.findIndex(img => img.src === imageSrc);
    
    image.src = imageSrc;
    captionElement.textContent = caption;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Add fade-in animation
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);
}

// Close lightbox
function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Navigate lightbox
function navigateLightbox(direction) {
    if (lightboxImages.length === 0) return;
    
    currentLightboxIndex += direction;
    
    if (currentLightboxIndex < 0) {
        currentLightboxIndex = lightboxImages.length - 1;
    } else if (currentLightboxIndex >= lightboxImages.length) {
        currentLightboxIndex = 0;
    }
    
    const currentImage = lightboxImages[currentLightboxIndex];
    document.getElementById('lightboxImage').src = currentImage.src;
    document.getElementById('lightboxCaption').textContent = currentImage.caption;
}

// Load more memories (placeholder for future pagination)
function loadMoreMemories() {
    // This would typically load more memories via AJAX
    console.log('Loading more memories...');
}

// Image preview function for form
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

// Smooth scroll animations
function addScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    document.querySelectorAll('.memory-card').forEach(card => {
        observer.observe(card);
    });
}

// Initialize scroll animations
document.addEventListener('DOMContentLoaded', addScrollAnimations);

// 🚀 Global Memory View Function
function viewMemory(memoryId) {
    window.location.href = `/memories/${memoryId}`;
}

// 🚀 Timeline Navigation Functionality
function setupTimelineNavigation() {
    const memoriesScroll = document.getElementById('memoriesScroll');
    const timelineBar = document.getElementById('timelineBar');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (!memoriesScroll || !prevBtn || !nextBtn) return;

    // Navigation buttons
    prevBtn.addEventListener('click', () => {
        memoriesScroll.scrollBy({ left: -320, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', () => {
        memoriesScroll.scrollBy({ left: 320, behavior: 'smooth' });
    });

    // Timeline memory point interactions (replaced month clicks)
    const memoryPoints = document.querySelectorAll('.timeline-memory-point');
    memoryPoints.forEach(point => {
        point.addEventListener('click', () => {
            const memoryId = point.dataset.memoryId;
            if (memoryId) {
                // Directly navigate to memory view
                window.location.href = `/memories/${memoryId}`;
            }
        });
    });

    // Update timeline on scroll - sync with memory cards
    let scrollTimeout;
    memoriesScroll.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            updateActiveMemoryPoint();
        }, 100);
    });

    function updateActiveMemoryPoint() {
        const cards = memoriesScroll.querySelectorAll('.memory-timeline-card');
        const memoryPoints = document.querySelectorAll('.timeline-memory-point');
        const scrollCenter = memoriesScroll.scrollLeft + memoriesScroll.offsetWidth / 2;
        
        let activeMemoryId = null;
        let closestDistance = Infinity;
        
        cards.forEach(card => {
            const cardCenter = card.offsetLeft + card.offsetWidth / 2;
            const distance = Math.abs(scrollCenter - cardCenter);
            
            if (distance < closestDistance) {
                closestDistance = distance;
                activeMemoryId = card.dataset.date; // Use date to match
            }
        });

        // Update active memory point
        memoryPoints.forEach(point => {
            point.classList.remove('active');
            if (point.dataset.date === activeMemoryId) {
                point.classList.add('active');
                
                // Auto-scroll timeline bar to show active memory point
                if (timelineBar) {
                    const barContainer = timelineBar.parentElement;
                    const pointRect = point.getBoundingClientRect();
                    const containerRect = barContainer.getBoundingClientRect();
                    
                    if (pointRect.left < containerRect.left || pointRect.right > containerRect.right) {
                        point.scrollIntoView({ 
                            behavior: 'smooth', 
                            inline: 'center',
                            block: 'nearest'
                        });
                    }
                }
            }
        });
    }

    // Initialize with first memory point active
    const firstMemoryPoint = document.querySelector('.timeline-memory-point');
    if (firstMemoryPoint) {
        firstMemoryPoint.classList.add('active');
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName.toLowerCase() === 'input') return;
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                memoriesScroll.scrollBy({ left: -320, behavior: 'smooth' });
                break;
            case 'ArrowRight':
                e.preventDefault();
                memoriesScroll.scrollBy({ left: 320, behavior: 'smooth' });
                break;
        }
    });
}
</script>
@endpush