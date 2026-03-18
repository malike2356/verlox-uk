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
  }

  // Theme toggle (dark / light with persistence)
  const root = document.documentElement;
  const themeToggle = document.getElementById('themeToggle');
  const themeText = themeToggle?.querySelector('.theme-toggle__text');

  const applyTheme = (theme) => {
    if (theme === 'light') {
      root.setAttribute('data-theme', 'light');
      if (themeText) themeText.textContent = 'Light';
    } else {
      root.setAttribute('data-theme', 'dark');
      if (themeText) themeText.textContent = 'Dark';
    }
  };

  const storedTheme = window.localStorage.getItem('verlox-theme');
  if (storedTheme === 'light' || storedTheme === 'dark') {
    applyTheme(storedTheme);
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
    applyTheme('light');
  } else {
    applyTheme('dark');
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const current = root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
      const next = current === 'light' ? 'dark' : 'light';
      window.localStorage.setItem('verlox-theme', next);
      applyTheme(next);
    });
  }
})();
