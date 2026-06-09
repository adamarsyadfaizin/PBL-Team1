<div class="room-media" data-room-carousel>
    <div class="room-media__stage">
        @foreach ($mediaItems as $index => $item)
            <div class="room-media__slide {{ $index === 0 ? 'is-active' : '' }}" data-room-slide>
                @if ($item['type'] === 'video')
                    <video controls preload="metadata" poster="{{ $item['poster'] ?? '' }}">
                        <source src="{{ $item['src'] }}" type="{{ $item['mime'] ?? 'video/mp4' }}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                @else
                    <img
                        src="{{ $item['src'] }}"
                        alt="{{ $item['label'] }}"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                        onerror="this.outerHTML = this.dataset.placeholder"
                        data-placeholder='<div class="room-media__placeholder"><svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span>Media kamar belum bisa dimuat</span></div>'
                    >
                @endif
            </div>
        @endforeach
    </div>

    @if (count($mediaItems) > 1)
        <button type="button" class="room-media__nav room-media__nav--prev" data-room-prev aria-label="Media sebelumnya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button type="button" class="room-media__nav room-media__nav--next" data-room-next aria-label="Media berikutnya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        <div class="room-media__dots" aria-label="Pilih media kamar">
            @foreach ($mediaItems as $index => $item)
                <button
                    type="button"
                    class="room-media__dot {{ $index === 0 ? 'is-active' : '' }}"
                    data-room-dot="{{ $index }}"
                    aria-label="Media {{ $index + 1 }}"
                ></button>
            @endforeach
        </div>
    @endif
</div>
