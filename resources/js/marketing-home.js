(() => {
    const year = document.getElementById('year');
    if (year) year.textContent = String(new Date().getFullYear());

    const nav = document.querySelector('.nav');
    const toggle = document.querySelector('.nav-toggle');
    if (nav && toggle) {
        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        });
        nav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open menu');
            });
        });
    }

    const root = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const themeText = themeToggle?.querySelector('.theme-toggle__text');

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';
        root.setAttribute('data-theme', isDark ? 'dark' : 'light');
        root.classList.toggle('dark', isDark);
        try {
            localStorage.setItem('verlox-theme', isDark ? 'dark' : 'light');
        } catch (_) {}
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', isDark ? '#0B1829' : '#f4f5fb');
        }
        if (themeText) {
            themeText.textContent = isDark ? 'Light' : 'Dark';
        }
    };

    const storedTheme = window.localStorage.getItem('verlox-theme');
    if (storedTheme === 'light' || storedTheme === 'dark') {
        applyTheme(storedTheme);
    } else {
        applyTheme('dark');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            applyTheme(next);
        });
    }

    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length && 'IntersectionObserver' in window) {
        const obs = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 },
        );
        revealEls.forEach((el) => obs.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }
})();
