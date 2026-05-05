  
    /* ── Scroll reveal ── */
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('visible'), i * 80);
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.10 });
    reveals.forEach(el => observer.observe(el));

    /* ── FAQ accordion ── */
    function toggleFaq(trigger) {
      const item = trigger.closest('.faq-item');
      const isOpen = item.classList.contains('open');

      // close all
      document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));

      // open clicked if it was closed
      if (!isOpen) item.classList.add('open');
    }

    /* ── Form submit ── */
    function handleSubmit(e) {
      e.preventDefault();
      const wrap = document.getElementById('contact-form-wrap');
      const form = wrap.querySelector('form');
      const success = document.getElementById('form-success');

      form.style.display = 'none';
      success.style.display = 'flex';
      success.style.animation = 'fadeUp .5s ease both';

      wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  
