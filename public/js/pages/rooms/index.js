(function () {
    'use strict';

    const form = document.querySelector('[data-rooms-search-form]');
    const input = document.querySelector('[data-rooms-search-input]');

    if (!form || !input) {
        return;
    }

    let timer = null;
    let lastValue = input.value;

    input.addEventListener('input', () => {
        window.clearTimeout(timer);

        timer = window.setTimeout(() => {
            if (input.value === lastValue) {
                return;
            }

            lastValue = input.value;
            form.submit();
        }, 450);
    });
}());
