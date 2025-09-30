// Scroll animations for landing page
document.addEventListener('DOMContentLoaded', function() {
    // Add animation classes to elements
    const animateElements = document.querySelectorAll('#landing .stats-card, #landing .feature-card, #landing .analytics-item, #landing .process-step, #landing .testimonial-card, #landing .accreditation-badge');
    
    animateElements.forEach((el, index) => {
        if (index % 3 === 0) {
            el.classList.add('scroll-animate-left');
        } else if (index % 3 === 1) {
            el.classList.add('scroll-animate');
        } else {
            el.classList.add('scroll-animate-right');
        }
    });

    // Section titles animate from bottom
    const sectionTitles = document.querySelectorAll('#landing .section-title');
    sectionTitles.forEach(title => {
        title.classList.add('scroll-animate');
    });

    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all animation elements
    document.querySelectorAll('#landing .scroll-animate, #landing .scroll-animate-left, #landing .scroll-animate-right').forEach(el => {
        observer.observe(el);
    });
});