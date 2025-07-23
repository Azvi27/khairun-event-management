// 🚀 ENHANCED MOBILE & DESKTOP INTERACTIONS
document.addEventListener('DOMContentLoaded', function() {
    
    // 🎬 Initialize scroll animations
    initializeScrollAnimations();
    
    // 🎯 Initialize navigation enhancements
    initializeNavigation();
    
    // Touch feedback for navigation items
    const navItems = document.querySelectorAll('.nav-item, .logout-btn');
    navItems.forEach(item => {
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        item.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Prevent zoom on input focus (iOS Safari)
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.style.fontSize = '16px';
        });
        
        input.addEventListener('blur', () => {
            input.style.fontSize = '';
        });
    });
    
    // Mobile header optimization
    const header = document.querySelector('.header');
    if (header && window.innerWidth <= 768) {
        // Simple touch scroll for header on mobile
        let startX = 0;
        let scrollLeft = 0;
        let isDragging = false;
        
        header.addEventListener('touchstart', function(e) {
            startX = e.touches[0].pageX;
            scrollLeft = header.scrollLeft;
            isDragging = true;
        });
        
        header.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            
            const x = e.touches[0].pageX;
            const walk = (startX - x) * 1.5;
            header.scrollLeft = scrollLeft + walk;
        });
        
        header.addEventListener('touchend', function() {
            isDragging = false;
        });
    }
    
    // Carousel touch gestures (if carousel exists)
    const carousel = document.querySelector('.carousel');
    if (carousel) {
        let startX = 0;
        let scrollLeft = 0;
        
        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX;
            scrollLeft = carousel.scrollLeft;
        });
        
        carousel.addEventListener('touchmove', (e) => {
            e.preventDefault();
            const x = e.touches[0].pageX;
            const walk = (startX - x) * 2;
            carousel.scrollLeft = scrollLeft + walk;
        });
    }
    
    // Optimize scroll performance with throttling
    let ticking = false;
    
    function updateOnScroll() {
        // Add any scroll-based updates here
        ticking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    });
    
    // Responsive navigation optimization
    function handleResize() {
        const header = document.querySelector('.header');
        if (header) {
            // Reset any inline styles on resize
            header.style.overflowX = window.innerWidth <= 768 ? 'auto' : 'visible';
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call
    
    // Accessibility improvements
    document.addEventListener('keydown', function(e) {
        // Skip to main content with Tab key
        if (e.key === 'Tab' && !e.shiftKey) {
            const firstNavItem = document.querySelector('.nav-item');
            if (document.activeElement === firstNavItem) {
                const mainContent = document.querySelector('.content-area');
                if (mainContent) {
                    mainContent.focus();
                }
            }
        }
    });
    
    // Performance monitoring (development only)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('🚀 Khairun Mobile Enhancements loaded');
        console.log('📱 Viewport:', window.innerWidth + 'x' + window.innerHeight);
        console.log('🎯 User Agent:', navigator.userAgent.includes('Mobile') ? 'Mobile' : 'Desktop');
    }
});

// 🎬 SCROLL ANIMATIONS SYSTEM
function initializeScrollAnimations() {
    let lastScrollY = window.scrollY;
    let ticking = false;
    
    function updateScrollState() {
        const header = document.querySelector('.header');
        const currentScrollY = window.scrollY;
        
        if (header) {
            // Add scrolled class when scrolled down
            if (currentScrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Hide header when scrolling down, show when scrolling up
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                header.classList.add('hidden');
            } else {
                header.classList.remove('hidden');
            }
        }
        
        lastScrollY = currentScrollY;
        ticking = false;
    }
    
    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(updateScrollState);
            ticking = true;
        }
    }
    
    // Throttled scroll listener
    window.addEventListener('scroll', onScroll, { passive: true });
    
    // Intersection Observer for card animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe animated elements
    const animatedElements = document.querySelectorAll(
        '.animate-card, .animate-hero, .animate-slide-left, .animate-slide-right, .animate-memory-card, .animate-memory-header'
    );
    
    animatedElements.forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
}

// 🎯 NAVIGATION ENHANCEMENTS
function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    // Enhanced navigation feedback
    navItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
        
        // Keyboard navigation support
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
    // Page transition effect
    const links = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not([target="_blank"])');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.hostname === window.location.hostname) {
                // Add loading state
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
                
                // Create page transition overlay
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                    z-index: 9999;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                    pointer-events: none;
                `;
                
                document.body.appendChild(overlay);
                
                setTimeout(() => {
                    overlay.style.opacity = '0.8';
                }, 10);
                
                // Clean up after navigation
                setTimeout(() => {
                    this.style.opacity = '';
                    this.style.pointerEvents = '';
                    overlay.remove();
                }, 1000);
            }
        });
    });
}

// 🎨 VISUAL FEEDBACK ENHANCEMENTS
function addRippleEffect(element, event) {
    const ripple = document.createElement('div');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(140, 224, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
        z-index: 1;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Add ripple to interactive elements
document.addEventListener('click', function(e) {
    const target = e.target.closest('.memory-card, .quick-action-card, .stat-card, .btn');
    if (target) {
        addRippleEffect(target, e);
    }
});

// Export for potential use in other modules
window.KhairunMobile = {
    version: '2.0.0',
    initialized: true
};