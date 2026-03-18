// ============================================
// Theme Toggle
// ============================================
(function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    function getInitialTheme() {
        const savedTheme = localStorage.getItem('verlox-theme');
        if (savedTheme) {
            return savedTheme;
        }
        // Default to dark theme for Verlox UK
        return 'dark';
    }
    
    function setTheme(theme) {
        if (theme === 'light') {
            body.classList.remove('dark-theme');
            body.classList.add('light-theme');
        } else {
            body.classList.remove('light-theme');
            body.classList.add('dark-theme');
        }
        localStorage.setItem('verlox-theme', theme);
    }
    
    const currentTheme = getInitialTheme();
    setTheme(currentTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const isLight = body.classList.contains('light-theme');
            setTheme(isLight ? 'dark' : 'light');
        });
    }
})();

// ============================================
// Smooth Scrolling
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        
        if (target) {
            const offsetTop = target.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ============================================
// Mobile Menu
// ============================================
(function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
        
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            });
        });
    }
})();

// ============================================
// Navbar Scroll Effect
// ============================================
(function() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });
})();

// ============================================
// Proposal Modal
// ============================================
(function() {
    const modal = document.getElementById('proposalModal');
    const proposalBtns = document.querySelectorAll('#proposalBtn, #heroProposalBtn');
    const closeBtn = document.querySelector('.close-modal');
    
    if (proposalBtns.length && modal) {
        proposalBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.add('active');
            });
        });
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('active');
            });
        }
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }
})();

// ============================================
// Contact Form
// ============================================
(function() {
    const contactForm = document.getElementById('contactForm');
    const proposalForm = document.getElementById('proposalForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = this.querySelector('#name').value;
            const email = this.querySelector('#email').value;
            const needs = this.querySelector('#needs').value;
            const message = this.querySelector('#message').value;
            
            if (!name || !email || !needs || !message) {
                alert('Please fill in all fields.');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }
            
            alert('Thank you for your message! We\'ll get back to you within 24 hours.\n\nIn the meantime, you can reach us at contact@verlox.uk');
            this.reset();
        });
    }
    
    if (proposalForm) {
        proposalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = this.querySelector('#proposal-name').value;
            const email = this.querySelector('#proposal-email').value;
            
            if (!name || !email) {
                alert('Please fill in all required fields.');
                return;
            }
            
            alert('Thank you for your proposal request! We\'ll prepare a custom proposal for you within 48 hours.');
            this.reset();
            
            const modal = document.getElementById('proposalModal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    }
})();

// ============================================
// Intersection Observer
// ============================================
(function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const animateElements = document.querySelectorAll('.feature-card, .professional-card, .built-card, .step');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
})();

// ============================================
// Active Navigation
// ============================================
(function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    function highlightNav() {
        let current = '';
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                current = sectionId;
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }
    
    window.addEventListener('scroll', highlightNav);
    highlightNav();
})();

// ============================================
// Add active class styles
// ============================================
(function() {
    const style = document.createElement('style');
    style.textContent = `
        .nav-menu a.active {
            color: var(--accent-primary);
        }
    `;
    document.head.appendChild(style);
})();

// ============================================
// Console Welcome
// ============================================
console.log('%c Verlox UK', 'font-size: 24px; font-weight: bold; color: #eab308;');
console.log('%c Engineering & delivery — powerful digital systems, built with security and clarity.', 'font-size: 14px; color: #94a3b8;');