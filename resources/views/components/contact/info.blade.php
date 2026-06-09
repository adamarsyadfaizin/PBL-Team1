@props(['guestProfile'])

@php
    $whatsappUrl = collect($guestProfile->platform_links ?: [])
        ->firstWhere('label', 'WhatsApp')['url'] ?? 'https://wa.me/6285707323326';
@endphp

<section class="contact-intro" aria-labelledby="contact-title">
    <div class="contact-shell">
        <div class="contact-help">
            <span class="section-label">{{ $guestProfile->contact_label }}</span>
            <h1 id="contact-title">{{ $guestProfile->contact_title }}</h1>
            <p>{{ $guestProfile->contact_description }}</p>
            <a href="#guest-request-form" class="btn-question">{{ $guestProfile->contact_button_label }}</a>
        </div>

        <section class="contact-faq" aria-labelledby="contact-faq-title">
            <div class="contact-section-head">
                <span class="section-label">{{ $guestProfile->contact_faq_label }}</span>
                <h2 id="contact-faq-title">{{ $guestProfile->contact_faq_title }}</h2>
                <p>{{ $guestProfile->contact_faq_description }}</p>
            </div>

            <div class="contact-faq-list" id="contactFaqList">
                @foreach (($guestProfile->contact_faqs ?: []) as $index => $faq)
                    <article class="contact-faq-item {{ $index === 0 ? 'is-open' : '' }}">
                        <button type="button" class="contact-faq-question" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                            <span>{{ $faq['question'] ?? '' }}</span>
                            <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="contact-faq-answer">
                            <p>{{ $faq['answer'] ?? '' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="contact-support-grid">
            <section class="contact-feedback" aria-labelledby="contact-feedback-title">
                <h2 id="contact-feedback-title">{{ $guestProfile->feedback_title }}</h2>
                @if (session('feedback_success'))
                    <p class="feedback-status">{{ session('feedback_success') }}</p>
                @endif
                <div class="feedback-actions" role="group" aria-label="Umpan balik informasi kontak">
                    <button type="button" class="feedback-btn" data-contact-feedback="yes">{{ $guestProfile->feedback_yes_label }}</button>
                    <button type="button" class="feedback-btn" data-contact-feedback="no">{{ $guestProfile->feedback_no_label }}</button>
                </div>

                <form class="feedback-input" id="contactFeedbackInput" method="POST" action="{{ route('contact.feedback.store') }}" @if (! $errors->has('masukkan')) hidden @endif>
                    @csrf
                    <h3>{{ $guestProfile->feedback_prompt }}</h3>
                    <textarea name="masukkan" rows="4" placeholder="Tulis masukan Anda di sini..." required>{{ old('masukkan') }}</textarea>
                    @error('masukkan')
                        <p class="feedback-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="feedback-submit">Kirim Masukan</button>
                </form>

                <div class="help-other">
                    <h3>{{ $guestProfile->feedback_help_title }}</h3>
                    <a class="btn-contact-wa" href="{{ $whatsappUrl }}?text=Halo%20Berlima%20Guest%20House,%20saya%20perlu%20bantuan" target="_blank" rel="noopener">
                        {{ $guestProfile->feedback_wa_label }}
                    </a>
                </div>
            </section>

            <div class="contact-info-grid">
                <section class="contact-info-block" aria-labelledby="contact-info-title">
                    <h2 id="contact-info-title">Informasi Kontak</h2>

                    <dl class="contact-list">
                        @foreach (($guestProfile->contact_items ?: []) as $item)
                            <div>
                                <dt>{{ $item['label'] ?? '' }}</dt>
                                <dd>
                                    @if (!empty($item['url']))
                                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener">{{ $item['value'] ?? '' }}</a>
                                    @else
                                        {{ $item['value'] ?? '' }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section class="contact-info-block" aria-labelledby="platform-title">
                    <h2 id="platform-title">{{ $guestProfile->platform_title }}</h2>
                    <p class="contact-block-copy">{{ $guestProfile->platform_description }}</p>

                    <div class="platform-links" aria-label="Platform digital Berlima Guest House">
                        @foreach (($guestProfile->platform_links ?: []) as $link)
                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener">{{ $link['label'] ?? '' }}</a>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>

    </div>
</section>
