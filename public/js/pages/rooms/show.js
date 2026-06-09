(function () {
    'use strict';

    const carousel = document.querySelector('[data-room-carousel]');
    if (!carousel) return;

    const slides = Array.from(carousel.querySelectorAll('[data-room-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-room-dot]'));
    const previous = carousel.querySelector('[data-room-prev]');
    const next = carousel.querySelector('[data-room-next]');
    let active = 0;

    function show(index) {
        active = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === active);

            const video = slide.querySelector('video');
            if (video && slideIndex !== active) {
                video.pause();
            }
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === active);
        });
    }

    previous?.addEventListener('click', () => show(active - 1));
    next?.addEventListener('click', () => show(active + 1));
    dots.forEach((dot, index) => dot.addEventListener('click', () => show(index)));
}());
