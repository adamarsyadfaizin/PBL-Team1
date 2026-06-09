@php
    $averageRating = round((float) ($room->reviews_avg_rating ?? 0), 1);
    $reviewsCount = (int) ($room->reviews_count ?? 0);

    $stars = function (float|int $rating): string {
        $rounded = (int) round((float) $rating);
        $output = '';

        for ($star = 1; $star <= 5; $star++) {
            $output .= '<span class="' . ($star <= $rounded ? 'is-filled' : '') . '">&#9733;</span>';
        }

        return $output;
    };
@endphp

<section class="room-panel room-reviews" id="room-reviews">
    <div class="room-panel__head room-panel__head--inline">
        <div>
            <span>Ulasan Kamar</span>
            <h2>Ulasan penyewa</h2>
        </div>

        @if ($reviewsCount > 0)
            <div class="room-reviews__score" aria-label="Rata-rata penilaian kamar">
                <strong>{{ number_format($averageRating, 1, ',', '.') }}</strong>
                <div>
                    <div class="review-stars" aria-hidden="true">{!! $stars($averageRating) !!}</div>
                    <span>{{ $reviewsCount }} ulasan</span>
                </div>
            </div>
        @endif
    </div>

    @if (session('review_success'))
        <div class="alert alert-success room-review-alert" role="alert">
            {{ session('review_success') }}
        </div>
    @endif

    @if ($reviewsCount === 0)
        <div class="room-review-empty room-review-empty--single">
            <div>
                <h3>Belum ada ulasan</h3>
                <p>Jadilah yang pertama memberikan ulasan untuk kamar {{ $room->nomor_kamar }}.</p>
            </div>

            @auth
                <form action="{{ route('rooms.reviews.store', ['room' => $room->nomor_kamar]) }}" method="POST" class="room-review-form room-review-form--inline">
                    @csrf
                    <div class="review-rating-input" role="radiogroup" aria-label="Rating kamar">
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <input
                                type="radio"
                                name="rating"
                                id="room-review-empty-rating-{{ $rating }}"
                                value="{{ $rating }}"
                                @checked((int) old('rating', 5) === $rating)
                            >
                            <label for="room-review-empty-rating-{{ $rating }}" aria-label="{{ $rating }} bintang">&#9733;</label>
                        @endfor
                    </div>
                    <textarea
                        id="room-review-empty-comment"
                        name="comment"
                        class="form-control @error('comment') is-invalid @enderror"
                        rows="3"
                        maxlength="1200"
                        required
                        placeholder="Tulis ulasan pertama untuk kamar ini"
                    >{{ old('comment') }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary room-review-submit">Kirim Ulasan</button>
                </form>
            @else
                <a href="{{ route('login', ['redirect' => url()->current() . '#room-reviews']) }}" class="btn btn-primary room-review-submit">Masuk untuk memberikan ulasan</a>
            @endauth
        </div>
    @else
        <div class="room-reviews__content">
            <div class="room-review-form-card">
                @auth
                    <h3>Tulis Ulasan</h3>
                    <p>Berikan penilaian dan komentar singkat tentang pengalaman Anda.</p>

                    <form action="{{ route('rooms.reviews.store', ['room' => $room->nomor_kamar]) }}" method="POST" class="room-review-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Penilaian</label>
                            <div class="review-rating-input" role="radiogroup" aria-label="Penilaian kamar">
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <input
                                        type="radio"
                                        name="rating"
                                        id="room-review-rating-{{ $rating }}"
                                        value="{{ $rating }}"
                                        @checked((int) old('rating', 5) === $rating)
                                    >
                                    <label for="room-review-rating-{{ $rating }}" aria-label="{{ $rating }} bintang">&#9733;</label>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="room-review-comment" class="form-label">Komentar</label>
                            <textarea
                                id="room-review-comment"
                                name="comment"
                                class="form-control @error('comment') is-invalid @enderror"
                                rows="4"
                                maxlength="1200"
                                required
                                placeholder="Tulis pengalaman Anda tentang kamar ini"
                            >{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary room-review-submit">Kirim Ulasan</button>
                    </form>
                @else
                    <h3>Tulis Ulasan</h3>
                    <p>Masuk terlebih dahulu untuk memberi penilaian dan komentar pada kamar ini.</p>
                    <a href="{{ route('login', ['redirect' => url()->current() . '#room-reviews']) }}" class="btn btn-primary room-review-submit">Masuk untuk memberikan ulasan</a>
                @endauth
            </div>

            <div class="room-review-list">
                @foreach ($reviews as $review)
                    <article class="room-review-item">
                        <div class="room-review-item__top">
                            <div>
                                <strong>{{ $review->user?->name ?: 'Pengguna Berlima' }}</strong>
                                <span>{{ $review->created_at?->format('d/m/Y') }}</span>
                            </div>
                            <div class="review-stars" aria-label="Penilaian {{ $review->rating }} dari 5">
                                {!! $stars($review->rating) !!}
                            </div>
                        </div>
                        <p>{{ $review->comment }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</section>
