// Hamburger Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const navButtons = document.getElementById('navButtons');
    
    if (hamburger && navButtons) {
        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            hamburger.classList.toggle('active');
            navButtons.classList.toggle('active');
        });

        // Close menu when a link is clicked
        navButtons.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navButtons.classList.remove('active');
            });
        });
    }
});

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    const hamburger = document.getElementById('hamburger');
    const navButtons = document.getElementById('navButtons');
    
    if (hamburger && navButtons) {
        if (!e.target.closest('nav')) {
            hamburger.classList.remove('active');
            navButtons.classList.remove('active');
        }
    }
});

// Smooth scroll untuk navigasi
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

// Navbar shadow on scroll
window.addEventListener('scroll', () => {
    const nav = document.querySelector('nav');
    if (window.scrollY > 50) {
        nav.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.05)';
    } else {
        nav.style.boxShadow = 'none';
    }
});

// Intersection Observer untuk animasi scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe feature cards
document.querySelectorAll('.feature-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
});

// Observe stat cards
document.querySelectorAll('.stat-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
});

document.addEventListener('DOMContentLoaded', function() {
    // Target akhir untuk setiap counter
    const targets = [10000, 50000, 99.9];
    
    // Elemen counter
    const counters = [
        document.getElementById('counter-1'),
        document.getElementById('counter-2'),
        document.getElementById('counter-3')
    ];
    
    // Durasi animasi (dalam milidetik)
    const duration = 2000;
    
    // Mulai semua counter dari 0
    counters.forEach((counter, index) => {
        if (index === 2) {
            counter.textContent = '0%'; // Untuk persentase
        } else {
            counter.textContent = '0'; // Untuk angka
        }
    });
    
    // Fungsi untuk memformat angka
    function formatNumber(num, isPercent = false) {
        if (isPercent) {
            return num.toFixed(1) + '%';
        }
        
        if (num >= 1000) {
            return (num / 1000).toFixed(0) + 'K+';
        }
        
        return num.toFixed(0);
    }
    
    // Fungsi animasi untuk setiap counter
    function animateCounter(counterElement, target, isPercent = false) {
        let startTime = null;
        const startValue = 0;
        
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            
            const progress = Math.min((timestamp - startTime) / duration, 1);
            
            // Easing function untuk animasi lebih natural
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            
            const currentValue = startValue + (target - startValue) * easeOutCubic;
            
            // Update teks dengan nilai yang sudah diformat
            counterElement.textContent = formatNumber(currentValue, isPercent);
            
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Pastikan nilai akhir tepat
                counterElement.textContent = formatNumber(target, isPercent);
            }
        }
        
        requestAnimationFrame(step);
    }
    
    // Jalankan animasi untuk semua counter dengan sedikit delay
    setTimeout(() => animateCounter(counters[0], targets[0], false), 200);
    setTimeout(() => animateCounter(counters[1], targets[1], false), 400);
    setTimeout(() => animateCounter(counters[2], targets[2], true), 600);
    
    // Opsional: Restart animasi saat di-klik
    counters.forEach((counter, index) => {
        counter.style.cursor = 'pointer';
        counter.addEventListener('click', function() {
            if (index === 2) {
                animateCounter(counter, targets[2], true);
            } else {
                animateCounter(counter, targets[index], false);
            }
        });
        
        // Tambahkan tooltip
        counter.title = 'Klik untuk restart animasi';
    });
});

const reveals = document.querySelectorAll('.reveal');

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, {
    threshold: 0.15
});

reveals.forEach(el => revealObserver.observe(el));
