function applyVerloxTheme(theme) {
    const root = document.documentElement;
    const isDark = theme === 'dark';
    root.setAttribute('data-theme', isDark ? 'dark' : 'light');
    root.classList.toggle('dark', isDark);
    try {
        localStorage.setItem('verlox-theme', isDark ? 'dark' : 'light');
    } catch (e) {}

    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
        meta.setAttribute('content', isDark ? '#0B1829' : '#f4f5fb');
    }

    document.querySelectorAll('[data-theme-label]').forEach((el) => {
        el.textContent = isDark ? 'Light' : 'Dark';
    });
}

function initVerloxTheme() {
    const stored = localStorage.getItem('verlox-theme');
    let theme = stored === 'light' || stored === 'dark' ? stored : 'dark';
    applyVerloxTheme(theme);

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-theme-toggle]');
        if (!btn) {
            return;
        }
        e.preventDefault();
        const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        applyVerloxTheme(next);
    });
}

document.addEventListener('DOMContentLoaded', initVerloxTheme);
