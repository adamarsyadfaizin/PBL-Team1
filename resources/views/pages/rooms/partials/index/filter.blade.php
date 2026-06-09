@php
    $statusOptions = [
        '' => 'Semua',
        'tersedia' => 'Tersedia',
        'terisi' => 'Terisi',
        'perbaikan' => 'Perbaikan',
    ];
@endphp

<div class="rooms-filter" id="filter-bar">
    <div class="rooms-filter__inner">
        <form class="rooms-search" method="GET" action="{{ route('rooms.index') }}" data-rooms-search-form>
            @if ($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            @if ($floor !== '')
                <input type="hidden" name="lantai" value="{{ $floor }}">
            @endif

            <label class="rooms-search__label" for="rooms-search-input">Cari kamar</label>
            <div class="rooms-search__control">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    id="rooms-search-input"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nomor, tipe, fasilitas..."
                    autocomplete="off"
                    data-rooms-search-input
                >
                @if ($search !== '')
                    <a href="{{ route('rooms.index', array_filter(['status' => $status, 'lantai' => $floor], fn ($value) => $value !== '')) }}" class="rooms-search__clear" aria-label="Hapus pencarian">×</a>
                @endif
            </div>
        </form>

        <div class="rooms-filter__chips" aria-label="Filter kamar">
            <span class="rooms-filter__label">Filter:</span>
            @foreach ($statusOptions as $value => $label)
                @php
                    $params = array_filter([
                        'q' => $search,
                        'status' => $value,
                        'lantai' => $floor,
                    ], fn ($item) => $item !== '');
                @endphp
                <a class="filter-chip {{ $status === $value ? 'filter-chip--active' : '' }}" href="{{ route('rooms.index', $params) }}">
                    {{ $label }}
                </a>
            @endforeach

            @foreach ($floors as $lantai)
                @php
                    $params = array_filter([
                        'q' => $search,
                        'status' => $status,
                        'lantai' => (string) $lantai === $floor ? '' : $lantai,
                    ], fn ($item) => $item !== '');
                @endphp
                <a class="filter-chip {{ (string) $lantai === $floor ? 'filter-chip--active' : '' }}" href="{{ route('rooms.index', $params) }}">
                    Lantai {{ $lantai }}
                </a>
            @endforeach
        </div>
    </div>
</div>
