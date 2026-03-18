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

  // Mailto form (simple + immediate hosting; no backend dependency)
  const form = document.getElementById('contactForm');
  const status = document.getElementById('formStatus');
  if (form instanceof HTMLFormElement) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const name = String(fd.get('name') || '').trim();
      const email = String(fd.get('email') || '').trim();
      const message = String(fd.get('message') || '').trim();

      if (!name || !email || !message) {
        if (status) status.textContent = 'Please complete all fields.';
        return;
      }

      const subject = encodeURIComponent(`Verlox UK enquiry — ${name}`);
      const body = encodeURIComponent(
        `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}\n`
      );
      const to = 'contact@velox.uk';
      window.location.href = `mailto:${to}?subject=${subject}&body=${body}`;
      if (status) status.textContent = 'Opening your email client…';
    });
  }
})();
