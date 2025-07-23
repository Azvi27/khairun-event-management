@extends('layouts.khairun')

@section('title', 'Timeline of Memories - Our Memories')

@push('styles')
<link href="{{ asset('css/memories.css') }}" rel="stylesheet">
<style>
/* 🚀 Enhanced Search & Filter Styles - CONSISTENT THEME */
.search-filter-section {
    margin: 2rem 0;
    padding: 2rem;
    background: linear-gradient(135deg, #181A26 0%, #262840 100%) !important;
    border-radius: 1.5rem;
    border: 2px solid rgba(140, 224, 255, 0.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15), 0 4px 15px rgba(140, 224, 255, 0.05);
}

.search-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.search-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #FFFFFF !important;
    font-family: 'Playfair Display', serif;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #262840 0%, #343646 100%);
    border: 2px solid rgba(140, 224, 255, 0.1);
    border-radius: 12px;
    color: #FFFFFF !important;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
}

.view-btn.active {
    background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%) !important;
    color: #1a202c !important;
    border-color: #8CE0FF;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(140, 224, 255, 0.4);
    font-weight: 600;
}

.search-controls {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    align-items: start;
}

.search-input-container {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    background: linear-gradient(135deg, #262840 0%, #343646 100%);
    border: 2px solid rgba(140, 224, 255, 0.1);
    border-radius: 12px;
    color: #FFFFFF !important;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #8CE0FF;
    box-shadow: 0 0 0 3px rgba(140, 224, 255, 0.1);
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.6) !important;
    pointer-events: none;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #262840 0%, #343646 100%);
    border: 2px solid rgba(140, 224, 255, 0.1);
    border-radius: 12px;
    color: #FFFFFF !important;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #8CE0FF;
}

.filter-select option {
    background: #262840 !important;
    color: #FFFFFF !important;
}

.clear-filters {
    padding: 0.75rem 1rem;
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 12px;
    color: #ef4444;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.clear-filters:hover {
    background: rgba(239, 68, 68, 0.3);
    transform: translateY(-1px);
}

.search-stats {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.9rem;
    color: var(--color-text-secondary);
}

/* 🎯 Grid View Styles */
.memories-grid-container {
    margin: 2rem 0;
}

.memories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem 0;
}

.memory-grid-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.memory-grid-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    border-color: rgba(59, 130, 246, 0.4);
}

.grid-card-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.grid-memory-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.grid-card-image:hover .grid-memory-image {
    transform: scale(1.05);
}

.grid-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.grid-card-image:hover .grid-image-overlay {
    opacity: 1;
}

.grid-overlay-icon {
    font-size: 2.5rem;
    color: white;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.grid-card-text {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    text-align: center;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
}

.grid-text-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.7;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.grid-text-preview {
    color: var(--color-text-primary);
    line-height: 1.5;
    font-size: 0.95rem;
}

.grid-card-info {
    padding: 1.5rem;
}

.grid-card-date {
    font-size: 0.9rem;
    color: var(--color-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.grid-card-description {
    color: var(--color-text-primary);
    line-height: 1.5;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.grid-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.grid-music-badge {
    font-size: 1.2rem;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.grid-card-actions {
    display: flex;
    gap: 0.5rem;
}

.grid-action-btn {
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: var(--color-text-primary);
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
}

.grid-action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* 🚀 Modern Lightbox Styles */
.lightbox-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.lightbox-overlay.active {
    opacity: 1;
}

.lightbox-container {
    max-width: 90vw;
    max-height: 90vh;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
}

.lightbox-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.lightbox-title {
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.lightbox-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.lightbox-content {
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.lightbox-image-container {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}

.lightbox-image {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.lightbox-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

.loading-text, .error-text {
    font-size: 0.9rem;
    opacity: 0.8;
}

.error-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.lightbox-navigation {
    position: absolute;
    top: 50%;
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 1rem;
    pointer-events: none;
}

.nav-btn {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    font-size: 2rem;
    padding: 1rem 1.5rem;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    pointer-events: auto;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.lightbox-info {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.lightbox-description {
    color: white;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.lightbox-actions {
    display: flex;
    gap: 1rem;
}

.lightbox-action-btn {
    background: rgba(59, 130, 246, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(59, 130, 246, 0.4);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.lightbox-action-btn:hover {
    background: rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .search-controls {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-controls {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .view-toggle {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .view-btn {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    
    .memories-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .lightbox-container {
        max-width: 95vw;
        max-height: 95vh;
    }
    
    .lightbox-image {
        max-height: 60vh;
    }
    
    .nav-btn {
        font-size: 1.5rem;
        padding: 0.75rem 1rem;
    }
    
    .lightbox-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .lightbox-action-btn {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .search-filter-section {
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .search-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .filter-select {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@section('content')
<h1 class="page-title">Timeline of Memories</h1>
<p class="page-subtitle">Click here — let the memories take you where words once lived.</p>

<!-- 🚀 NEW: Advanced Search & Filter Section -->
<section class="search-filter-section">
    <div class="search-container">
        <div class="search-header">
            <h3 class="search-title">🔍 Find Your Memories</h3>
            <div class="view-toggle">
                <button class="view-btn active" data-view="timeline" onclick="switchView('timeline')">
                    📅 Timeline
                </button>
                <button class="view-btn" data-view="grid" onclick="switchView('grid')">
                    🎯 Grid
                </button>
            </div>
        </div>
        
        <div class="search-controls">
            <div class="search-input-container">
                <input type="text" id="searchInput" placeholder="Search memories..." class="search-input">
                <span class="search-icon">🔍</span>
            </div>
            
            <div class="filter-controls">
                <select id="sortFilter" class="filter-select">
                    <option value="date-desc">📅 Newest First</option>
                    <option value="date-asc">📅 Oldest First</option>
                    <option value="created-desc">⭐ Recently Added</option>
                </select>
                
                <select id="typeFilter" class="filter-select">
                    <option value="all">📋 All Types</option>
                    <option value="with-image">🖼️ With Images</option>
                    <option value="with-music">🎵 With Music</option>
                    <option value="text-only">📝 Text Only</option>
                </select>
                
                <input type="month" id="dateFilter" class="filter-select" title="Filter by month">
                
                <button class="clear-filters" onclick="clearAllFilters()">🗑️ Clear</button>
            </div>
        </div>
        
        <div class="search-stats" style="padding: 1rem 0; text-align: center;">
            <span id="searchResults" style="color: #8CE0FF !important; font-size: 1rem; font-weight: 600; background: rgba(140, 224, 255, 0.1); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid rgba(140, 224, 255, 0.2);">{{ $memories->count() }} memories found</span>
        </div>
    </div>
</section>

<!-- Our Journey Section -->
<section class="journey-section">
    <h2 class="section-title">Our Journey</h2>
    <p class="journey-subtitle">Swipe through our beautiful memories timeline</p>
    
    @if($memories->count() > 0)
        <!-- 🚀 NEW: Timeline Navigation Bar -->
        <div class="timeline-nav" style="display: block !important; visibility: visible !important; opacity: 1 !important;">
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

        <!-- 🚀 NEW: Grid View Container (Hidden by default) -->
        <div class="memories-grid-container" id="memoriesGrid" style="display: none;">
            <div class="memories-grid">
                @foreach($memories as $memory)
                    <div class="memory-grid-card" 
                         data-date="{{ $memory->memory_date->format('Y-m-d') }}" 
                         data-month="{{ $memory->memory_date->format('Y-m') }}"
                         data-description="{{ strtolower($memory->description) }}">
                        
                        @if($memory->image_path)
                            <div class="grid-card-image">
                                <img src="{{ asset('storage/' . $memory->image_path) }}" 
                                     alt="Memory from {{ $memory->memory_date->format('F d, Y') }}" 
                                     class="grid-memory-image"
                                     loading="lazy"
                                     onclick="openLightbox('{{ asset('storage/' . $memory->image_path) }}', '{{ $memory->description }}')">
                                <div class="grid-image-overlay">
                                    <span class="grid-overlay-icon">🔍</span>
                                </div>
                            </div>
                        @else
                            <div class="grid-card-text">
                                <div class="grid-text-icon">📝</div>
                                <div class="grid-text-preview">
                                    {{ Str::limit($memory->description, 80) }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid-card-info">
                            <div class="grid-card-date">
                                {{ $memory->memory_date->format('M d, Y') }}
                            </div>
                            
                            <div class="grid-card-description">
                                {{ Str::limit($memory->description, 60) }}
                            </div>
                            
                            <div class="grid-card-meta">
                                @if($memory->spotify_track_id)
                                    <span class="grid-music-badge">🎵</span>
                                @endif
                                
                                <div class="grid-card-actions">
                                    <a href="{{ route('memories.show', $memory) }}" class="grid-action-btn" title="View">👁️</a>
                                    <a href="{{ route('memories.edit', $memory) }}" class="grid-action-btn" title="Edit">✏️</a>
                                    <form method="POST" action="{{ route('memories.destroy', $memory) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="grid-action-btn" title="Delete"
                                                onclick="return confirm('Delete this memory?')">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 🚀 ENHANCED: Horizontal Memories Scroll Container -->
        <div class="memories-timeline-container" id="memoriesTimeline" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
            <div class="memories-horizontal-scroll" id="memoriesScroll" style="display: flex !important; visibility: visible !important; opacity: 1 !important;">
                @foreach($memories as $memory)
                    <div class="memory-timeline-card" 
                         data-date="{{ $memory->memory_date->format('Y-m-d') }}" 
                         data-month="{{ $memory->memory_date->format('Y-m') }}"
                         data-description="{{ strtolower($memory->description) }}"
                         style="display: block !important; visibility: visible !important; opacity: 1 !important; min-width: 280px !important;">
                        
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

// 🚀 NEW: Modern Lightbox Implementation
function openLightbox(imageSrc, description, memoryId = null) {
    // Create lightbox HTML if not exists
    if (!document.getElementById('memoryLightbox')) {
        const lightboxHTML = `
            <div id="memoryLightbox" class="lightbox-overlay">
                <div class="lightbox-container">
                    <div class="lightbox-header">
                        <div class="lightbox-title">Memory Details</div>
                        <button class="lightbox-close" onclick="closeLightbox()">✕</button>
                    </div>
                    
                    <div class="lightbox-content">
                        <div class="lightbox-image-container">
                            <img id="lightboxImage" src="" alt="Memory Image" class="lightbox-image">
                            <div class="lightbox-loading">
                                <div class="loading-spinner"></div>
                                <div class="loading-text">Loading...</div>
                            </div>
                        </div>
                        
                        <div class="lightbox-navigation">
                            <button class="nav-btn nav-prev" onclick="navigateLightbox(-1)">‹</button>
                            <button class="nav-btn nav-next" onclick="navigateLightbox(1)">›</button>
                        </div>
                        
                        <div class="lightbox-info">
                            <div id="lightboxDescription" class="lightbox-description"></div>
                            <div class="lightbox-actions">
                                <button class="lightbox-action-btn" onclick="downloadImage()">
                                    📥 Download
                                </button>
                                <button class="lightbox-action-btn" onclick="shareMemory()">
                                    📤 Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
        setupLightboxEvents();
    }
    
    // Show lightbox
    const lightbox = document.getElementById('memoryLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxDescription = document.getElementById('lightboxDescription');
    const lightboxLoading = lightbox.querySelector('.lightbox-loading');
    
    // Reset and show loading
    lightboxLoading.style.display = 'flex';
    lightboxImage.style.display = 'none';
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Set current memory data
    window.currentLightboxMemory = {
        imageSrc: imageSrc,
        description: description,
        memoryId: memoryId
    };
    
    // Load image
    lightboxImage.onload = function() {
        lightboxLoading.style.display = 'none';
        lightboxImage.style.display = 'block';
        lightbox.classList.add('active');
    };
    
    lightboxImage.onerror = function() {
        lightboxLoading.innerHTML = '<div class="error-icon">📷</div><div class="error-text">Image not available</div>';
    };
    
    lightboxImage.src = imageSrc;
    lightboxDescription.textContent = description;
    
    // Prepare navigation data
    prepareNavigationData();
}

function closeLightbox() {
    const lightbox = document.getElementById('memoryLightbox');
    if (lightbox) {
        lightbox.classList.remove('active');
        setTimeout(() => {
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

function setupLightboxEvents() {
    const lightbox = document.getElementById('memoryLightbox');
    
    // Close on overlay click
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                navigateLightbox(-1);
                break;
            case 'ArrowRight':
                e.preventDefault();
                navigateLightbox(1);
                break;
        }
    });
}

function prepareNavigationData() {
    // Get all memories with images for navigation
    const memoryCards = document.querySelectorAll('.memory-timeline-card, .memory-grid-card');
    window.lightboxMemories = [];
    
    memoryCards.forEach(card => {
        const img = card.querySelector('.memory-image, .grid-memory-image');
        if (img && img.style.display !== 'none') {
            const description = card.querySelector('.memory-title, .grid-card-description')?.textContent || '';
            window.lightboxMemories.push({
                imageSrc: img.src,
                description: description,
                element: card
            });
        }
    });
    
    // Find current index
    window.currentLightboxIndex = window.lightboxMemories.findIndex(
        memory => memory.imageSrc === window.currentLightboxMemory.imageSrc
    );
}

function navigateLightbox(direction) {
    if (!window.lightboxMemories || window.lightboxMemories.length === 0) return;
    
    window.currentLightboxIndex += direction;
    
    // Loop around
    if (window.currentLightboxIndex >= window.lightboxMemories.length) {
        window.currentLightboxIndex = 0;
    } else if (window.currentLightboxIndex < 0) {
        window.currentLightboxIndex = window.lightboxMemories.length - 1;
    }
    
    const nextMemory = window.lightboxMemories[window.currentLightboxIndex];
    if (nextMemory) {
        openLightbox(nextMemory.imageSrc, nextMemory.description);
    }
}

function downloadImage() {
    const image = document.getElementById('lightboxImage');
    if (image.src) {
        const link = document.createElement('a');
        link.href = image.src;
        link.download = 'memory-image.jpg';
        link.click();
    }
}

function shareMemory() {
    if (navigator.share) {
        navigator.share({
            title: 'Memory',
            text: window.currentLightboxMemory.description,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// 🚀 NEW: Search & Filter Functionality
function initializeSearchAndFilter() {
    const searchInput = document.getElementById('searchInput');
    const sortFilter = document.getElementById('sortFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput) return; // Exit if search elements don't exist yet
    
    // Search input handler
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterMemories();
        }, 300);
    });
    
    // Filter change handlers
    [sortFilter, typeFilter, dateFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', filterMemories);
        }
    });
    
    function filterMemories() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortFilter.value;
        const typeFilterValue = typeFilter.value;
        const dateFilterValue = dateFilter.value;
        
        // Get all memory cards (both timeline and grid)
        const timelineCards = document.querySelectorAll('.memory-timeline-card');
        const gridCards = document.querySelectorAll('.memory-grid-card');
        
        let visibleCount = 0;
        
        // Filter timeline cards
        timelineCards.forEach(card => {
            const shouldShow = shouldShowCard(card, searchTerm, typeFilterValue, dateFilterValue);
            card.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) visibleCount++;
        });
        
        // Filter grid cards
        gridCards.forEach(card => {
            const shouldShow = shouldShowCard(card, searchTerm, typeFilterValue, dateFilterValue);
            card.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Update results count
        if (searchResults) {
            searchResults.textContent = `${visibleCount} memories found`;
    searchResults.style.color = '#8CE0FF';
    searchResults.style.fontWeight = '600';
        }
    }
    
    function shouldShowCard(card, searchTerm, typeFilter, dateFilter) {
        // Text search
        if (searchTerm && !card.dataset.description.includes(searchTerm)) {
            return false;
        }
        
        // Type filter
        if (typeFilter !== 'all') {
            const hasImage = card.querySelector('.memory-image, .grid-memory-image');
            const hasMusic = card.querySelector('.memory-music, .grid-music-badge');
            
            switch (typeFilter) {
                case 'with-image':
                    if (!hasImage) return false;
                    break;
                case 'with-music':
                    if (!hasMusic) return false;
                    break;
                case 'text-only':
                    if (hasImage) return false;
                    break;
            }
        }
        
        // Date filter
        if (dateFilter) {
            const cardMonth = card.dataset.month;
            if (cardMonth !== dateFilter) {
                return false;
            }
        }
        
        return true;
    }
    
    // Make function globally accessible
    window.filterMemories = filterMemories;
}

function switchView(viewType) {
    const timelineContainer = document.querySelector('.memories-timeline-container');
    const gridContainer = document.getElementById('memoriesGrid');
    const timelineNav = document.querySelector('.timeline-nav');
    const viewButtons = document.querySelectorAll('.view-btn');
    
    console.log('Switching to view:', viewType);
    console.log('Timeline container found:', timelineContainer);
    console.log('Grid container found:', gridContainer);
    
    // Update button states
    viewButtons.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === viewType);
    });
    
    // Switch views
    if (viewType === 'grid') {
        if (timelineContainer) {
            timelineContainer.style.display = 'none';
            timelineContainer.style.visibility = 'hidden';
        }
        if (gridContainer) {
            gridContainer.style.display = 'block';
            gridContainer.style.visibility = 'visible';
        }
        if (timelineNav) {
            timelineNav.style.display = 'none';
            timelineNav.style.visibility = 'hidden';
        }
    } else {
        if (timelineContainer) {
            timelineContainer.style.display = 'block';
            timelineContainer.style.visibility = 'visible';
            timelineContainer.style.opacity = '1';
        }
        if (gridContainer) {
            gridContainer.style.display = 'none';
            gridContainer.style.visibility = 'hidden';
        }
        if (timelineNav) {
            timelineNav.style.display = 'block';
            timelineNav.style.visibility = 'visible';
            timelineNav.style.opacity = '1';
        }
    }
}

function clearAllFilters() {
    const searchInput = document.getElementById('searchInput');
    const sortFilter = document.getElementById('sortFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (searchInput) searchInput.value = '';
    if (sortFilter) sortFilter.value = 'date-desc';
    if (typeFilter) typeFilter.value = 'all';
    if (dateFilter) dateFilter.value = '';
    
    if (window.filterMemories) {
        window.filterMemories();
    }
}

// Initialize on page load - ENHANCED
document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing memory navigation
    const memoriesScroll = document.getElementById('memoriesScroll');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (memoriesScroll && prevBtn && nextBtn) {
        setupMemoryNavigation();
    }
    
    // Initialize new search and filter functionality
    initializeSearchAndFilter();
    
    // Add smooth loading animation for cards
    const memoryCards = document.querySelectorAll('.memory-timeline-card, .memory-grid-card');
    memoryCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
});

// Add CSS animation keyframes
if (!document.querySelector('style[data-memory-animations]')) {
    const style = document.createElement('style');
    style.setAttribute('data-memory-animations', 'true');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .memory-timeline-card, .memory-grid-card {
            opacity: 1 !important;
            display: block !important;
            visibility: visible !important;
        }
    `;
    document.head.appendChild(style);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Memory page initialized');
    
    // Force timeline visibility
    const timelineContainer = document.querySelector('.memories-timeline-container');
    const timelineNav = document.querySelector('.timeline-nav');
    
    if (timelineContainer) {
        timelineContainer.style.display = 'block';
        timelineContainer.style.visibility = 'visible';
        timelineContainer.style.opacity = '1';
        console.log('✅ Timeline container forced visible');
    }
    
    if (timelineNav) {
        timelineNav.style.display = 'block';
        timelineNav.style.visibility = 'visible';
        timelineNav.style.opacity = '1';
        console.log('✅ Timeline navigation forced visible');
    }
    
    // Initialize search functionality
    initializeSearchAndFilter();
    
    // Set timeline as default view
    switchView('timeline');
});

// Also initialize if already loaded
if (document.readyState !== 'loading') {
    console.log('🚀 DOM already ready, initializing immediately');
    setTimeout(() => {
        const timelineContainer = document.querySelector('.memories-timeline-container');
        const timelineNav = document.querySelector('.timeline-nav');
        
        if (timelineContainer) {
            timelineContainer.style.display = 'block';
            timelineContainer.style.visibility = 'visible';
            timelineContainer.style.opacity = '1';
            console.log('✅ Timeline container forced visible (immediate)');
        }
        
        if (timelineNav) {
            timelineNav.style.display = 'block';
            timelineNav.style.visibility = 'visible';
            timelineNav.style.opacity = '1';
            console.log('✅ Timeline navigation forced visible (immediate)');
        }
        
        initializeSearchAndFilter();
        switchView('timeline');
    }, 50);
}
</script>
@endpush