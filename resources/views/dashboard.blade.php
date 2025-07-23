@extends('layouts.khairun')

@section('title', 'Dashboard - Our Memories')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
<style>
/* 🚀 ENHANCED: Modern Glassmorphism Dashboard */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    max-width: 900px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.quick-action-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 2rem 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-decoration: none;
    color: inherit;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    box-shadow: 
        0 8px 32px rgba(31, 38, 135, 0.15),
        0 4px 16px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.1) 0%, 
        rgba(147, 51, 234, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 20px;
}

.quick-action-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 
        0 20px 40px rgba(59, 130, 246, 0.25),
        0 10px 30px rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(59, 130, 246, 0.4);
    text-decoration: none;
    color: inherit;
}

.quick-action-card:hover::before {
    opacity: 1;
}

.action-icon {
    font-size: 2.5rem;
    flex-shrink: 0;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s ease;
    z-index: 2;
    position: relative;
}

.quick-action-card:hover .action-icon {
    transform: scale(1.1) rotate(5deg);
}

.action-text {
    z-index: 2;
    position: relative;
}

.action-text h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--color-text-primary);
    letter-spacing: -0.025em;
}

.action-text p {
    margin: 0;
    font-size: 0.95rem;
    color: var(--color-text-secondary);
    opacity: 0.85;
    font-weight: 500;
}

/* 🎨 Enhanced Statistics Section */
.stats-section {
    margin: 4rem 0;
    padding: 0 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.stat-card {
    position: relative;
    background: linear-gradient(135deg, 
        var(--color-primary) 0%, 
        var(--color-primary-dark) 50%,
        #1e40af 100%);
    color: white;
    padding: 2.5rem 2rem;
    border-radius: 24px;
    text-align: center;
    box-shadow: 
        0 10px 30px rgba(59, 130, 246, 0.3),
        0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 
        0 20px 40px rgba(59, 130, 246, 0.4),
        0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
    transition: transform 0.3s ease;
    position: relative;
    z-index: 2;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(-5deg);
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-number {
    font-size: 3rem;
    font-weight: 900;
    line-height: 1;
    display: block;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #ffffff, #e0f2fe);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.stat-label {
    font-size: 1rem;
    opacity: 0.95;
    margin-top: 0.5rem;
    font-weight: 600;
    letter-spacing: 0.025em;
    text-transform: uppercase;
    font-size: 0.875rem;
}

/* 🎯 Enhanced Hero Section */
.hero {
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 40%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(147, 51, 234, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.hero-title {
    position: relative;
    z-index: 2;
}

.cta-button {
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

/* 🌟 Mobile Optimization */
@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .quick-action-card {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem 1rem;
        gap: 1rem;
    }
    
    .action-icon {
        font-size: 2.25rem;
    }
    
    .action-text h3 {
        font-size: 1.1rem;
    }
    
    .action-text p {
        font-size: 0.85rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .stat-card {
        padding: 2rem 1.5rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .stat-icon {
        font-size: 2.5rem;
    }
}

@media (max-width: 480px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .quick-action-card {
        flex-direction: row;
        text-align: left;
        padding: 1.25rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* ✨ Loading Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.quick-action-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.quick-action-card:nth-child(1) { animation-delay: 0.1s; }
.quick-action-card:nth-child(2) { animation-delay: 0.2s; }
.quick-action-card:nth-child(3) { animation-delay: 0.3s; }
.quick-action-card:nth-child(4) { animation-delay: 0.4s; }

.stat-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.stat-card:nth-child(1) { animation-delay: 0.2s; }
.stat-card:nth-child(2) { animation-delay: 0.3s; }
.stat-card:nth-child(3) { animation-delay: 0.4s; }
.stat-card:nth-child(4) { animation-delay: 0.5s; }

/* ⚡ ENHANCED LOADING OVERLAY */
.page-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease;
}

.page-loader.fade-out {
    opacity: 0;
    pointer-events: none;
}

.loader-content {
    text-align: center;
    color: white;
}

.loader-spinner {
    width: 60px;
    height: 60px;
    border: 3px solid rgba(140, 224, 255, 0.3);
    border-top: 3px solid #8CE0FF;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.loader-text {
    font-size: 1.1rem;
    font-weight: 500;
    color: #8CE0FF;
    opacity: 0.9;
}
</style>
@endpush

@push('scripts')
<script>
// 🚀 ENHANCED PAGE LOADING EXPERIENCE
document.addEventListener('DOMContentLoaded', function() {
    // Remove page loader with smooth transition
    setTimeout(() => {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            loader.classList.add('fade-out');
            setTimeout(() => {
                loader.remove();
            }, 500);
        }
    }, 800);

    // 🎯 Enhanced button interactions
    addButtonLoadingStates();
    
    // ✨ Add scroll animations
    addScrollAnimations();
    
    // 🎨 Enhanced visual feedback
    addVisualFeedback();
});

function addButtonLoadingStates() {
    const buttons = document.querySelectorAll('a[href], button');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Skip if it's an external link or opens in new tab
            if (this.target === '_blank' || this.href?.startsWith('http')) return;
            
            // Add loading state
            this.classList.add('btn-loading');
            this.style.pointerEvents = 'none';
            
            // Remove loading state after navigation (or timeout)
            setTimeout(() => {
                this.classList.remove('btn-loading');
                this.style.pointerEvents = 'auto';
            }, 2000);
        });
    });
}

function addScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.animate-card, .animate-hero, .animate-slide-left, .animate-slide-right').forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
}

function addVisualFeedback() {
    // Add ripple effect to interactive elements
    document.querySelectorAll('.quick-action-card, .memory-card, .stat-card').forEach(card => {
        card.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.classList.add('ripple-effect');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// 🌟 Enhanced stat number animation
function animateNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(number => {
        const finalValue = parseInt(number.textContent);
        const duration = 2000;
        const increment = finalValue / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= finalValue) {
                current = finalValue;
                clearInterval(timer);
            }
            number.textContent = Math.floor(current);
        }, 16);
    });
}

// Start number animation when stats section is visible
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumbers();
                observer.unobserve(entry.target);
            }
        });
    });
    
    observer.observe(statsSection);
}

// 🎨 Add ripple effect styles
const rippleCSS = `
.ripple-effect {
    position: absolute;
    border-radius: 50%;
    background: rgba(140, 224, 255, 0.3);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(2);
        opacity: 0;
    }
}
`;

const style = document.createElement('style');
style.textContent = rippleCSS;
document.head.appendChild(style);
</script>
@endpush

@section('content')
<!-- ⚡ ENHANCED PAGE LOADER -->
<div class="page-loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <div class="loader-text">Loading your memories...</div>
    </div>
</div>

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

    <!-- 🚀 ENHANCED: Hero Section -->
    <section class="hero mb-16 animate-hero">
        <h1 class="hero-title text-center">
            A journey told in memories — stitched together by time, and always open for the stories you choose to keep.
        </h1>
        
        <!-- ✅ ORIGINAL BUTTON TETAP ADA -->
        <div class="text-center mt-8">
            <a href="{{ route('memories.create') }}" class="cta-button hover-pulse glass-enhanced">
                ➕ Add New Memory
            </a>
        </div>
        
        <!-- 🚀 ENHANCED: Quick Actions Grid -->
        <div class="quick-actions-grid">
            <a href="{{ route('memories.create') }}" class="quick-action-card animate-card animate-stagger-1 hover-bounce">
                <div class="action-icon">📸</div>
                <div class="action-text">
                    <h3>Add Memory</h3>
                    <p>Capture a moment</p>
                </div>
            </a>
            
            <a href="{{ route('birthday-surprises.create') }}" class="quick-action-card animate-card animate-stagger-2 hover-bounce">
                <div class="action-icon">🎁</div>
                <div class="action-text">
                    <h3>Create Surprise</h3>
                    <p>Plan something special</p>
                </div>
            </a>
            
            <a href="{{ route('calendar') }}" class="quick-action-card animate-card animate-stagger-3 hover-bounce">
                <div class="action-icon">📅</div>
                <div class="action-text">
                    <h3>Add Event</h3>
                    <p>Schedule together</p>
                </div>
            </a>
            
            <a href="{{ route('music.index') }}" class="quick-action-card animate-card animate-stagger-4 hover-bounce">
                <div class="action-icon">🎵</div>
                <div class="action-text">
                    <h3>Our Playlist</h3>
                    <p>Find perfect song</p>
                </div>
            </a>
        </div>
    </section>

    <!-- 🚀 ENHANCED: Statistics Section -->
    @if(isset($stats) && $stats)
    <section class="stats-section animate-slide-left">
        <div class="stats-grid">
            <div class="stat-card animate-card animate-stagger-1 hover-pulse">
                <div class="stat-icon">💝</div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['total_memories'] ?? 0 }}</span>
                    <div class="stat-label">Total Memories</div>
                </div>
            </div>
            
            <div class="stat-card animate-card animate-stagger-2 hover-pulse">
                <div class="stat-icon">🌟</div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['days_together'] ?? 0 }}</span>
                    <div class="stat-label">Days Together</div>
                </div>
            </div>
            
            <div class="stat-card animate-card animate-stagger-3 hover-pulse">
                <div class="stat-icon">🎁</div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['pending_surprises'] ?? 0 }}</span>
                    <div class="stat-label">Surprises Waiting</div>
                </div>
            </div>
            
            <div class="stat-card animate-card animate-stagger-4 hover-pulse">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['memories_this_month'] ?? 0 }}</span>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- ✅ SEMUA EXISTING CONTENT TETAP SAMA -->
    <!-- Main Content Grid -->
    <div class="grid gap-16">
        <!-- ✅ Memories Timeline Section - TIDAK DIUBAH -->
        <section class="content-section animate-slide-right">
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
                        <article class="memory-card animate-card animate-stagger-{{ $loop->index % 6 + 1 }} hover-bounce" onclick="window.location.href='{{ route('memories.show', $memory) }}'" style="cursor: pointer;">
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

        <!-- ✅ Calendar Section - TIDAK DIUBAH -->
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

        <!-- ✅ Countdown Section - TIDAK DIUBAH -->
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

        <!-- ✅ Music Section - TIDAK DIUBAH -->
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
</script>
@endpush