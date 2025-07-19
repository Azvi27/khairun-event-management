// Mobile Touch Enhancements - OPTIMIZED VERSION
document.addEventListener('DOMContentLoaded', function() {
    
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

// Export for potential use in other modules
window.KhairunMobile = {
    version: '2.0.0',
    initialized: true
};