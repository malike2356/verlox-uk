(() => {
  // ── Year ────────────────────────────────────────────────────────────────────
  const year = document.getElementById('year');
  if (year) year.textContent = String(new Date().getFullYear());

  // ── Mobile nav ──────────────────────────────────────────────────────────────
  const nav    = document.querySelector('.nav');
  const toggle = document.querySelector('.nav-toggle');
  if (nav && toggle) {
    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
    // Close menu when a link is clicked
    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open menu');
      });
    });
  }

  // ── Theme ────────────────────────────────────────────────────────────────────
  const root        = document.documentElement;
  const themeToggle = document.getElementById('themeToggle');
  const themeText   = themeToggle?.querySelector('.theme-toggle__text');

  const applyTheme = (theme) => {
    root.setAttribute('data-theme', theme === 'light' ? 'light' : 'dark');
    // Label shows what you will switch TO
    if (themeText) themeText.textContent = theme === 'light' ? 'Dark' : 'Light';
  };

  const storedTheme = window.localStorage.getItem('verlox-theme');
  if (storedTheme === 'light' || storedTheme === 'dark') {
    applyTheme(storedTheme);
  } else if (window.matchMedia?.('(prefers-color-scheme: light)').matches) {
    applyTheme('light');
  } else {
    applyTheme('dark');
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
      window.localStorage.setItem('verlox-theme', next);
      applyTheme(next);
    });
  }

  // ── Scroll reveal ─────────────────────────────────────────────────────────────
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length && 'IntersectionObserver' in window) {
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    revealEls.forEach(el => obs.observe(el));
  } else {
    // No IO support — show everything immediately
    revealEls.forEach(el => el.classList.add('is-visible'));
  }

  // ── Contact form (AJAX) ───────────────────────────────────────────────────────
  const contactForm = document.getElementById('contactForm');
  const tsInput     = document.getElementById('formTs');
  const statusEl    = document.getElementById('formStatus');

  if (tsInput) tsInput.value = String(Date.now());

  if (contactForm && tsInput) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      tsInput.value = String(Date.now());

      const btn = contactForm.querySelector('[type="submit"]');
      const origLabel = btn.textContent;
      btn.disabled    = true;
      btn.textContent = 'Sending…';
      if (statusEl) { statusEl.textContent = ''; statusEl.className = 'form__status'; }

      try {
        const res  = await fetch('contact.php', {
          method:  'POST',
          headers: { Accept: 'application/json' },
          body:    new FormData(contactForm),
        });
        const data = await res.json();

        if (res.ok && data.ok) {
          statusEl.textContent = '✓ Message sent — we\'ll reply from contact@verlox.uk.';
          statusEl.className   = 'form__status form__status--ok';
          contactForm.reset();
          if (tsInput) tsInput.value = String(Date.now());
        } else {
          throw new Error(data.message || 'Something went wrong. Please try again.');
        }
      } catch (err) {
        if (statusEl) {
          statusEl.textContent = err.message || 'Something went wrong. Please try again.';
          statusEl.className   = 'form__status form__status--err';
        }
      } finally {
        btn.disabled    = false;
        btn.textContent = origLabel;
      }
    });
  }
})();
