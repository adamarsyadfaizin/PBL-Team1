@extends('layouts.app')

@section('title', 'Kontak - Berlima Guest House')

@section('body-class', 'contact-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')
    <div class="contact-page-wrap">
        <x-contact.info :guest-profile="$guestProfile" />

        <section class="contact-main" id="contact-form">
            <div class="contact-main-inner">
                <div class="contact-form-panel">
                    <div class="form-head">
                        <span class="section-label">Kirim Pesan</span>
                        <h2 class="section-title">Sampaikan kebutuhan Anda</h2>
                        <p class="section-sub">
                            Formulir ini tetap terhubung ke sistem permintaan tamu. Tim kami akan meninjau pesan
                            dan menghubungi Anda melalui WhatsApp.
                        </p>
                    </div>

                    <div id="contact-form-wrap">
                        <x-contact.form :rooms="$rooms" />
                    </div>
                </div>

                <x-contact.map :guest-profile="$guestProfile" />
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.contact-faq-question').forEach((button) => {
        button.addEventListener('click', () => {
            const item = button.closest('.contact-faq-item');
            const isOpen = item.classList.contains('is-open');

            document.querySelectorAll('.contact-faq-item').forEach((faqItem) => {
                faqItem.classList.remove('is-open');
                faqItem.querySelector('.contact-faq-question')?.setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.querySelectorAll('[data-contact-feedback]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-contact-feedback]').forEach((item) => item.classList.remove('is-selected'));
            button.classList.add('is-selected');

            const feedbackInput = document.getElementById('contactFeedbackInput');
            if (!feedbackInput) return;

            feedbackInput.hidden = button.dataset.contactFeedback !== 'no';
        });
    });
</script>
@endpush
