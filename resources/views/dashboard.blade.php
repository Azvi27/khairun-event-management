@extends('layouts.khairun')

@section('title', 'Dashboard - Our Memories')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Success Login Message -->
    @if(session('success'))
        <div class="mb-8 p-4 text-center" style="
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); 
            color: var(--color-secondary); 
            border-radius: var(--radius-md); 
            font-weight: var(--font-semibold);
            box-shadow: var(--shadow-sm);
        ">
            🎉 {{ session('success') }}
        </div>
    @endif

    <!-- Hero Section -->
    <section class="hero mb-16">
        <h1 class="hero-title text-center">
            A journey told in memories — stitched together by time, and always open for the stories you choose to keep.
        </h1>
        <div class="text-center mt-8">
            <a href="{{ route('memories.create') }}" class="cta-button">
                ➕ Add New Memory
            </a>
        </div>
    </section>

    <!-- Main Content Grid -->
    <div class="grid gap-16">
        <!-- Memories Timeline Section -->
        <section class="content-section">
            <header class="mb-8">
                <h2 class="section-title text-left">📸 Our Gallery of Memories</h2>
                <p class="text-body-large text-left" style="color: var(--color-text-secondary); opacity: 0.8;">
                    Every moment captured, every story preserved in our digital scrapbook
                </p>
            </header>
            
            @if($memories->count() > 0)
                <!-- 🚀 Horizontal Memory Container with Side Navigation -->
                <div class="memories-carousel-wrapper">
                    <button class="nav-arrow nav-arrow-left" id="dashboardPrevBtn">←</button>
                    
                    <div class="memory-timeline-wrapper">
                        <div class="memory-timeline-horizontal" id="dashboardMemoriesContainer">
                    @foreach($memories as $memory)
                        <article class="memory-card" onclick="window.location.href='{{ route('memories.show', $memory) }}'" style="cursor: pointer;">
                            <header class="memory-date">
                                📅 {{ $memory->memory_date->format('d F Y') }}
                            </header>
                            
                            <!-- Memory Preview -->
                            <div class="memory-preview">
                                @if($memory->image_path)
                                    <div class="memory-image-container">
                                        <img src="{{ asset('storage/' . $memory->image_path) }}" 
                                             alt="Memory from {{ $memory->memory_date->format('F d, Y') }}" 
                                             class="current-image"
                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'text-align:center; padding:20px; color:#64748b;\'><div style=\'font-size:2rem;\'>📷</div><div>Image not available</div></div>';">
                                        <div class="memory-image-overlay">
                                            <span class="memory-overlay-text">📸</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="memory-text-preview">
                                        <div class="memory-text-icon">💭</div>
                                        <div class="memory-text-content">
                                            "{{ Str::limit($memory->description, 80) }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="memory-description">
                                {{ Str::limit($memory->description, 60) }}
                            </div>
                            
                            <footer class="memory-actions">
                                <span class="memory-action-hint">👁️ Click to view</span>
                            </footer>
                        </article>
                    @endforeach
                        </div>
                    </div>
                    
                    <button class="nav-arrow nav-arrow-right" id="dashboardNextBtn">→</button>
                </div>
            @else
                <div class="memory-empty-state">
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

        <!-- Calendar Section -->
        <section class="content-section calendar-section">
            <header class="mb-8">
                <h2 class="section-title text-left">📅 Calendar Overview</h2>
                <p class="text-body-large text-left" style="color: var(--color-text-secondary); opacity: 0.8;">
                    Keep track of your important events and milestones
                </p>
            </header>
            
            @if(isset($calendarData) && isset($monthlyEvents) && isset($upcomingEvents))
            <div class="calendar-widget" onclick="window.location.href='{{ route('calendar') }}'" style="cursor: pointer;">
                <div class="calendar-header">
                    <h3 class="calendar-month">{{ $calendarData['monthName'] ?? now()->format('F Y') }}</h3>
                    <span class="calendar-link-hint">👆 Click to open full calendar</span>
                </div>
                
                <div class="mini-calendar">
                    <div class="calendar-days-header">
                        <div class="day-header">Sun</div>
                        <div class="day-header">Mon</div>
                        <div class="day-header">Tue</div>
                        <div class="day-header">Wed</div>
                        <div class="day-header">Thu</div>
                        <div class="day-header">Fri</div>
                        <div class="day-header">Sat</div>
                    </div>
                    
                    <div class="calendar-days-grid">
                        @php
                            $startOfMonth = $calendarData['firstDay'] ?? now()->startOfMonth();
                            $endOfMonth = $calendarData['lastDay'] ?? now()->endOfMonth();
                            $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                            $endOfCalendar = $endOfMonth->copy()->endOfWeek();
                            $currentDate = $startOfCalendar->copy();
                        @endphp
                        
                        @while($currentDate <= $endOfCalendar)
                            @php
                                $dateString = $currentDate->format('Y-m-d');
                                $dayEvents = isset($monthlyEvents) ? ($monthlyEvents[$dateString] ?? collect()) : collect();
                                $isCurrentMonth = $currentDate->month === ($calendarData['month'] ?? now()->month);
                                $isToday = $currentDate->isToday();
                            @endphp
                            
                            <div class="calendar-day 
                                {{ !$isCurrentMonth ? 'other-month' : '' }}
                                {{ $isToday ? 'today' : '' }}
                                {{ $dayEvents->count() > 0 ? 'has-events' : '' }}">
                                <span class="day-number">{{ $currentDate->day }}</span>
                                @if($dayEvents->count() > 0)
                                    <div class="event-dots">
                                        @foreach($dayEvents->take(3) as $event)
                                            <span class="event-dot {{ $event->type }}" title="{{ $event->title }}"></span>
                                        @endforeach
                                        @if($dayEvents->count() > 3)
                                            <span class="event-more">+{{ $dayEvents->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            @php
                                $currentDate->addDay();
                            @endphp
                        @endwhile
                    </div>
                </div>
            </div>
            
            @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
                <div class="upcoming-events">
                    <h4 class="upcoming-title">📋 Upcoming Events</h4>
                    <div class="events-preview">
                        @foreach($upcomingEvents as $event)
                            <div class="event-preview" onclick="window.location.href='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                                <div class="event-icon">{{ $event->getTypeIcon() }}</div>
                                <div class="event-info">
                                    <div class="event-name">{{ $event->title }}</div>
                                    <div class="event-date">{{ $event->start_date->format('M d, Y') }} at {{ $event->start_date->format('H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="calendar-actions">
                        <a href="{{ route('calendar') }}" class="view-calendar-btn">
                            📅 View Full Calendar
                        </a>
                    </div>
                </div>
            @else
                <div class="no-events">
                    <div class="no-events-icon">📅</div>
                    <div class="no-events-text">
                        <h4>No upcoming events</h4>
                        <p>Add some events to your calendar to see them here</p>
                        <a href="{{ route('calendar') }}" class="add-event-btn">
                            ➕ Add Event
                        </a>
                    </div>
                </div>
            @endif
            @else
                <!-- Fallback when calendar data is not available -->
                <div class="calendar-fallback">
                    <div class="fallback-content">
                        <div class="fallback-icon">📅</div>
                        <h3>Calendar Overview</h3>
                        <p>Calendar data is loading...</p>
                        <a href="{{ route('calendar') }}" class="view-calendar-btn">
                            📅 Open Calendar
                        </a>
                    </div>
                </div>
            @endif
        </section>

        <!-- Countdown Section -->
        <section class="countdown-section">
            @if($nextSurprise)
                <header class="mb-8">
                    <h2 class="countdown-title text-center">
                        🎁 Countdown to your next surprise!
                    </h2>
                </header>
                
                <div class="timer-container" onclick="window.location.href='{{ route('birthday-surprises.show', $nextSurprise->id) }}'" 
                     style="cursor: pointer;" role="button" tabindex="0" 
                     aria-label="Click to view surprise details">
                    <div class="timer-box" id="days" aria-label="Days remaining">0</div>
                    <div class="timer-box" id="hours" aria-label="Hours remaining">0</div>
                    <div class="timer-box" id="minutes" aria-label="Minutes remaining">0</div>
                    <div class="timer-box" id="seconds" aria-label="Seconds remaining">0</div>
                </div>
                
                <div class="timer-labels">
                    <span>Days</span>
                    <span>Hours</span>
                    <span>Minutes</span>
                    <span>Seconds</span>
                </div>
                
                <footer class="surprise-info">
                    🎈 Surprise from {{ $nextSurprise->sender->name ?? 'Someone special' }} 
                    on {{ $nextSurprise->reveal_at->format('d F Y, H:i') }}
                </footer>
            @else
                <header class="text-center">
                    <h2 class="countdown-title">
                        🎁 No upcoming surprises... Create one!
                    </h2>
                    <div class="mt-8">
                        <a href="{{ route('birthday-surprises.create') }}" class="cta-button">
                            🎁 Create Surprise
                        </a>
                    </div>
                </header>
            @endif
        </section>

        <!-- Music Section -->
        <section class="content-section music-section" role="button" tabindex="0" 
                 onclick="window.location.href='{{ route('music.index') }}'" 
                 style="cursor: pointer;">
            <header class="mb-8 text-center">
                <h2 class="section-title">🎵 Our Soundtrack</h2>
                <p class="music-description">
                    For everything I couldn't say, I made a playlist instead.
                </p>
                <p class="text-body-medium" style="color: var(--color-text-secondary); opacity: 0.7;">
                    Click here — let the music take you where words once lived.
                </p>
            </header>
            
            <div class="playlist">
                <div class="song-item">
                    <div class="song-cover" aria-hidden="true">🎵</div>
                    <div class="song-info">
                        <div class="song-title">Melukis Senja</div>
                        <div class="song-artist">Budi Doremi</div>
                    </div>
                </div>
                <div class="song-item">
                    <div class="song-cover" aria-hidden="true">🎶</div>
                    <div class="song-info">
                        <div class="song-title">Hati-Hati di Jalan</div>
                        <div class="song-artist">Tulus</div>
                    </div>
                </div>
                <div class="song-item">
                    <div class="song-cover" aria-hidden="true">🎤</div>
                    <div class="song-info">
                        <div class="song-title">Perfect</div>
                        <div class="song-artist">Ed Sheeran</div>
                    </div>
                </div>
                <div class="song-item">
                    <div class="song-cover" aria-hidden="true">❤️</div>
                    <div class="song-info">
                        <div class="song-title">Right Here Waiting</div>
                        <div class="song-artist">Richard Marx</div>
                    </div>
                </div>
            </div>
            
            <footer class="mt-6 text-center">
                <span class="text-caption" style="color: var(--color-primary); opacity: 0.8;">
                    🎵 Click to explore our full playlist
                </span>
            </footer>
        </section>
    </div>
@endsection

@push('scripts')
<script>
// Enhanced Countdown Timer with better accessibility
document.addEventListener('DOMContentLoaded', function() {
    @if($nextSurprise)
        const targetDate = new Date('{{ $nextSurprise->reveal_at }}').getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                const daysEl = document.getElementById('days');
                const hoursEl = document.getElementById('hours');
                const minutesEl = document.getElementById('minutes');
                const secondsEl = document.getElementById('seconds');
                
                if (daysEl) {
                    daysEl.textContent = days.toString().padStart(2, '0');
                    daysEl.setAttribute('aria-label', `${days} days remaining`);
                }
                if (hoursEl) {
                    hoursEl.textContent = hours.toString().padStart(2, '0');
                    hoursEl.setAttribute('aria-label', `${hours} hours remaining`);
                }
                if (minutesEl) {
                    minutesEl.textContent = minutes.toString().padStart(2, '0');
                    minutesEl.setAttribute('aria-label', `${minutes} minutes remaining`);
                }
                if (secondsEl) {
                    secondsEl.textContent = seconds.toString().padStart(2, '0');
                    secondsEl.setAttribute('aria-label', `${seconds} seconds remaining`);
                }
            } else {
                // Countdown finished - redirect to surprise
                window.location.href = '{{ route('birthday-surprises.show', $nextSurprise->id) }}';
            }
        }
        
        updateCountdown();
        const countdownInterval = setInterval(updateCountdown, 1000);
        
        // Cleanup interval on page unload
        window.addEventListener('beforeunload', function() {
            clearInterval(countdownInterval);
        });
    @endif
    
    // 🚀 Dashboard Horizontal Navigation - with multiple initialization attempts
    setTimeout(() => setupDashboardNavigation(), 50);   // First attempt
    setTimeout(() => setupDashboardNavigation(), 200);  // Second attempt  
    setTimeout(() => setupDashboardNavigation(), 500);  // Third attempt (fallback)
    
    // Enhanced keyboard navigation
    const clickableElements = document.querySelectorAll('[role="button"][tabindex="0"]');
    clickableElements.forEach(element => {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                element.click();
            }
        });
    });
    
    // Add subtle loading animation for memory cards (optional)
    const memoryCards = document.querySelectorAll('.memory-card');
    memoryCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'slideUp 0.6s ease-out';
    });
});

// 🚀 Dashboard Navigation - Show Only Complete Cards (No Cropping)
function setupDashboardNavigation() {
    // Prevent multiple setups
    if (window.dashboardNavigationSetup) return;
    
    const container = document.getElementById('dashboardMemoriesContainer');
    const wrapper = container?.parentElement; // memory-timeline-wrapper
    const prevBtn = document.getElementById('dashboardPrevBtn');
    const nextBtn = document.getElementById('dashboardNextBtn');
    
    if (!container || !wrapper || !prevBtn || !nextBtn) return;
    
    let currentIndex = 0;
    const maxCards = container.children.length;
    
    if (maxCards === 0) return;
    
    // Mark as setup to prevent duplicate initialization
    window.dashboardNavigationSetup = true;
    
    // Use ResizeObserver for reliable parent size detection
    function setupResizeObserver() {
        const parentContainer = wrapper.parentElement;
        if (!parentContainer) return;
        
        const resizeObserver = new ResizeObserver(entries => {
            for (let entry of entries) {
                // Parent container size changed, recalculate
                requestAnimationFrame(() => {
                    updatePosition(false);
                });
            }
        });
        
        resizeObserver.observe(parentContainer);
        
        // Initial calculation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                updatePosition(false);
            });
        });
    }
    
    function getCardDimensions() {
        // Match exact CSS breakpoints for consistency
        let cardWidth, gap;
        
        if (window.innerWidth >= 1400) {
            cardWidth = 400;
            gap = 32;
        } else if (window.innerWidth >= 1200) {
            cardWidth = 350;
            gap = 32;
        } else if (window.innerWidth >= 900) {
            cardWidth = 320;
            gap = 32;
        } else {
            cardWidth = 280;
            gap = 24;
        }
        
        return {
            width: cardWidth,
            gap: gap,
            total: cardWidth + gap
        };
    }
    
    function calculateVisibleCards() {
        // Get the actual available space more reliably
        const carouselWrapper = wrapper.parentElement; // .memories-carousel-wrapper
        const contentSection = carouselWrapper?.parentElement; // .content-section
        
        // Calculate available width: content section - arrows - gaps - padding
        let availableWidth;
        if (contentSection && contentSection.offsetWidth > 0) {
            availableWidth = contentSection.offsetWidth - 150; // Space for arrows + padding
        } else {
            availableWidth = window.innerWidth - 300; // Fallback
        }
        
        const { total } = getCardDimensions();
        
        // Calculate cards that can fit
        const possibleCards = Math.floor(availableWidth / total);
        const finalCount = Math.max(1, Math.min(possibleCards, maxCards));
        

        
        return finalCount;
    }
    
    function updateWrapperWidth() {
        const visibleCards = calculateVisibleCards();
        const { width, gap } = getCardDimensions();
        
        // Calculate exact width needed (no padding since removed from CSS)
        const exactWidth = (width * visibleCards) + (gap * (visibleCards - 1));
        
        // Check if CSS width is already correct (±10px tolerance)
        const currentWidth = wrapper.offsetWidth;
        const widthDifference = Math.abs(currentWidth - exactWidth);
        
        // Only override CSS if there's a significant difference
        if (visibleCards > 0 && exactWidth > 0 && widthDifference > 10) {
            wrapper.style.width = `${exactWidth}px`;
            wrapper.style.maxWidth = `${exactWidth}px`;
            wrapper.style.minWidth = `${exactWidth}px`;
        }
        
        return visibleCards;
    }
    
    function getMaxIndex() {
        const visibleCards = calculateVisibleCards();
        return Math.max(0, maxCards - visibleCards);
    }
    
    function updatePosition(animate = true) {
        const visibleCards = updateWrapperWidth();
        const { total } = getCardDimensions();
        const translateX = -currentIndex * total;
        
        if (animate) {
            container.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        } else {
            container.style.transition = 'none';
        }
        
        container.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        const maxIndex = getMaxIndex();
        
        if (maxCards <= visibleCards) {
            // All cards are visible, hide buttons
            prevBtn.style.opacity = '0.3';
            nextBtn.style.opacity = '0.3';
            prevBtn.style.pointerEvents = 'none';
            nextBtn.style.pointerEvents = 'none';
        } else {
            // Enable buttons for navigation
            prevBtn.style.opacity = '1';
            nextBtn.style.opacity = '1';
            prevBtn.style.pointerEvents = 'auto';
            nextBtn.style.pointerEvents = 'auto';
        }
        

    }
    
    prevBtn.addEventListener('click', () => {
        const visibleCards = calculateVisibleCards();
        if (maxCards <= visibleCards) return;
        
        currentIndex--;
        
        // Infinite loop: if at beginning, go to end
        if (currentIndex < 0) {
            currentIndex = getMaxIndex();
        }
        
        updatePosition();
    });
    
    nextBtn.addEventListener('click', () => {
        const visibleCards = calculateVisibleCards();
        if (maxCards <= visibleCards) return;
        
        const maxIndex = getMaxIndex();
        currentIndex++;
        
        // Infinite loop: if at end, go to beginning
        if (currentIndex > maxIndex) {
            currentIndex = 0;
        }
        
        updatePosition();
    });
    
    // Keyboard navigation with infinite loop
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName.toLowerCase() === 'input') return;
        
        const visibleCards = calculateVisibleCards();
        if (maxCards <= visibleCards) return;
        
        const maxIndex = getMaxIndex();
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                currentIndex--;
                if (currentIndex < 0) {
                    currentIndex = maxIndex;
                }
                updatePosition();
                break;
            case 'ArrowRight':
                e.preventDefault();
                currentIndex++;
                if (currentIndex > maxIndex) {
                    currentIndex = 0;
                }
                updatePosition();
                break;
        }
    });
    
    // Initialize with ResizeObserver for reliable detection
    setupResizeObserver();
    
    // Fallback for older browsers or edge cases
    window.addEventListener('load', () => {
        setTimeout(() => {
            updatePosition(false);
        }, 100);
    });
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const maxIndex = getMaxIndex();
            if (currentIndex > maxIndex) {
                currentIndex = Math.max(0, maxIndex);
            }
            updatePosition(false);
        }, 100);
    });
}

// Add slideUp animation keyframes - FIXED: Memory cards are now visible by default
if (!document.querySelector('style[data-dashboard-animations]')) {
    const style = document.createElement('style');
    style.setAttribute('data-dashboard-animations', 'true');
    style.textContent = `
        @keyframes slideUp {
            from {
                opacity: 0.7;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}
</script>
@endpush