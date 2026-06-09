(function () {
    'use strict';

    const form = document.querySelector('[data-booking-stepper]');
    if (!form) return;

    const panels = Array.from(form.querySelectorAll('[data-step-panel]'));
    const indicators = Array.from(form.querySelectorAll('[data-step-indicator]'));
    const previous = form.querySelector('[data-step-prev]');
    const next = form.querySelector('[data-step-next]');
    const submit = form.querySelector('[data-step-submit]');
    const type = form.querySelector('#tipe_sewa');
    const checkIn = form.querySelector('#tanggal_check_in');
    const checkOut = form.querySelector('#tanggal_check_out');
    const durationDisplay = form.querySelector('#durasi_display');
    const totalDisplay = form.querySelector('#total_display');
    const paymentTotalDisplay = form.querySelector('#payment_total_display');
    const dateWarning = form.querySelector('[data-date-warning]');
    const dateWarningTitle = form.querySelector('[data-date-warning-title]');
    const dateWarningText = form.querySelector('[data-date-warning-text]');
    const bookingConflicts = JSON.parse(form.dataset.bookingConflicts || '[]');
    const rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });
    const parseAmount = (value) => Number(String(value || '0').replace(',', '.')) || 0;

    let active = Math.max(0, Math.min(Number(form.dataset.initialStep || 1) - 1, panels.length - 1));

    function show(index) {
        active = Math.max(0, Math.min(index, panels.length - 1));

        panels.forEach((panel, panelIndex) => {
            panel.classList.toggle('is-active', panelIndex === active);
        });

        indicators.forEach((indicator, indicatorIndex) => {
            indicator.classList.toggle('is-active', indicatorIndex === active);
            indicator.classList.toggle('is-complete', indicatorIndex < active);
        });

        previous.style.display = active === 0 ? 'none' : 'inline-flex';
        next.style.display = active === panels.length - 1 ? 'none' : 'inline-flex';
        submit.style.display = active === panels.length - 1 ? 'inline-flex' : 'none';
        updateDateWarning();
    }

    function validateCurrentPanel() {
        const fields = Array.from(panels[active].querySelectorAll('input, select, textarea'));

        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return false;
            }
        }

        return true;
    }

    function daysBetween() {
        if (!checkIn.value || !checkOut.value) return 0;

        const start = new Date(checkIn.value + 'T00:00:00');
        const end = new Date(checkOut.value + 'T00:00:00');

        return Math.ceil((end - start) / 86400000);
    }

    function updateEstimate() {
        const days = Math.max(0, daysBetween());
        const monthly = type.value === 'bulanan';
        const duration = monthly ? Math.max(1, Math.ceil(days / 30)) : Math.max(1, days);
        const unitPrice = parseAmount(monthly ? form.dataset.monthly : form.dataset.daily);
        const deposit = parseAmount(form.dataset.deposit);
        const total = days > 0 ? (unitPrice * duration) + deposit : 0;

        durationDisplay.value = monthly ? duration + ' bulan' : duration + ' malam';
        totalDisplay.textContent = rupiah.format(total);

        if (paymentTotalDisplay) {
            paymentTotalDisplay.textContent = rupiah.format(total);
        }

        if (checkIn.value) {
            const minCheckout = new Date(checkIn.value + 'T00:00:00');
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkOut.min = minCheckout.toISOString().slice(0, 10);

            if (checkOut.value && checkOut.value <= checkIn.value) {
                checkOut.value = checkOut.min;
            }
        }

        updateDateWarning();
    }

    function selectedDateConflict() {
        if (!checkIn.value || !checkOut.value) return null;

        const selectedStart = new Date(checkIn.value + 'T00:00:00');
        const selectedEnd = new Date(checkOut.value + 'T00:00:00');
        const overlaps = bookingConflicts.filter((booking) => {
            const bookingStart = new Date(booking.check_in + 'T00:00:00');
            const bookingEnd = new Date(booking.check_out + 'T00:00:00');

            return selectedStart <= bookingEnd && selectedEnd >= bookingStart;
        });

        if (overlaps.some((booking) => booking.status === 'active_stay')) {
            return {
                type: 'blocked',
                bookings: overlaps.filter((booking) => booking.status === 'active_stay'),
            };
        }

        if (overlaps.length > 0) {
            return {
                type: 'tentative',
                bookings: overlaps,
            };
        }

        return null;
    }

    function formatDateRange(booking) {
        const start = new Date(booking.check_in + 'T00:00:00');
        const end = new Date(booking.check_out + 'T00:00:00');
        const formatter = new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });

        return formatter.format(start) + ' - ' + formatter.format(end);
    }

    function updateDateWarning() {
        if (!dateWarning || !dateWarningTitle || !dateWarningText) return;

        const conflict = selectedDateConflict();

        if (!conflict) {
            dateWarning.hidden = true;
            dateWarning.classList.remove('booking-date-warning--blocked', 'booking-date-warning--notice');
            submit.disabled = false;
            return;
        }

        const ranges = conflict.bookings.map(formatDateRange).join(', ');
        dateWarning.hidden = false;
        dateWarning.classList.toggle('booking-date-warning--blocked', conflict.type === 'blocked');
        dateWarning.classList.toggle('booking-date-warning--notice', conflict.type !== 'blocked');

        if (conflict.type === 'blocked') {
            dateWarningTitle.textContent = 'Tanggal ini sudah final untuk penyewa lain';
            dateWarningText.textContent = 'Periode ' + ranges + ' sudah difinalkan dan belum keluar, jadi tanggal ini tidak bisa dipesan lagi.';
            submit.disabled = true;
            return;
        }

        dateWarningTitle.textContent = 'Ada reservasi lain pada tanggal yang sama';
        dateWarningText.textContent = 'Periode ' + ranges + ' sudah keluar atau diajukan penyewa lain dan menunggu konfirmasi admin. Jika Anda tetap mengirim reservasi, admin akan memproses berdasarkan bukti pembayaran dan finalisasi tercepat.';
        submit.disabled = false;
    }

    previous.addEventListener('click', () => show(active - 1));
    next.addEventListener('click', () => {
        if (validateCurrentPanel()) {
            show(active + 1);
        }
    });

    [type, checkIn, checkOut].forEach((field) => field.addEventListener('change', updateEstimate));

    updateEstimate();
    show(active);
}());
