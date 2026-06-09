@php
    $activeBooking = $isActive
        ? $upcomingBookings->firstWhere('id', $availability?->booking_aktif_id)
        : null;

    $statusLabel = function (string $status): string {
        return match ($status) {
            'pending' => 'Sudah diajukan',
            'menunggu_konfirmasi' => 'Menunggu konfirmasi',
            'active_stay' => 'Belum Keluar',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    };
@endphp

<div class="room-detail__body">
    <section class="room-panel room-panel--about">
        <div class="room-panel__head">
            <span>Tentang Kamar</span>
            <h2>Detail dan fasilitas kamar</h2>
        </div>

        <p>
            {{ $room->deskripsi ?: 'Kamar ini dirancang untuk hunian praktis dengan area tidur, penyimpanan, dan fasilitas utama yang menunjang kebutuhan harian.' }}
        </p>

        <div class="room-about__amenities">
            @forelse ($room->fasilitas as $facility)
                <span class="amenity-chip">{{ $facility }}</span>
            @empty
                <span class="amenity-chip amenity-chip--more">Fasilitas belum diisi admin</span>
            @endforelse
        </div>
    </section>

    <section class="room-panel room-panel--availability">
        <div class="room-panel__head room-panel__head--inline">
            <div>
                <span>Ketersediaan Kamar</span>
                <h2>Status dan jadwal pemesanan</h2>
            </div>
            <span class="room-status {{ $isActive ? 'room-status--occupied' : 'room-status--available' }}">
                {{ $isActive ? 'Belum Keluar' : 'Tersedia' }}
            </span>
        </div>

        <div class="availability-summary">
            <div>
                <span>Status</span>
                <strong>{{ $isActive ? 'Sedang digunakan penyewa lain' : 'Tidak ada pemesanan aktif hari ini' }}</strong>
            </div>
            <div>
                <span>Periode aktif</span>
                <strong>
                    @if ($isActive)
                        {{ $date($availability->tanggal_check_in) }} - {{ $date($availability->tanggal_check_out) }}
                    @else
                        -
                    @endif
                </strong>
            </div>
            <div>
                <span>Tipe sewa</span>
                <strong>{{ $activeBooking ? ucfirst($activeBooking->tipe_sewa) : '-' }}</strong>
            </div>
            <div>
                <span>Durasi</span>
                <strong>
                    @if ($activeBooking)
                        {{ $activeBooking->durasi }} {{ $activeBooking->tipe_sewa === 'bulanan' ? 'bulan' : 'malam' }}
                    @else
                        -
                    @endif
                </strong>
            </div>
        </div>

        <div class="availability-schedule">
            <div class="availability-schedule__head">
                <h3>Jadwal terdekat</h3>
                <span>{{ $upcomingBookings->count() }} jadwal</span>
            </div>

            @forelse ($upcomingBookings->take(3) as $booking)
                <div class="availability-row">
                    <div>
                        <strong>{{ $booking->tanggal_check_in->format('d/m/Y') }} - {{ $booking->tanggal_check_out->format('d/m/Y') }}</strong>
                        <span>{{ $statusLabel($booking->status) }} - {{ ucfirst($booking->tipe_sewa) }}</span>
                    </div>
                    <em>{{ $booking->durasi }} {{ $booking->tipe_sewa === 'bulanan' ? 'bulan' : 'malam' }}</em>
                </div>
            @empty
                <p>Belum ada jadwal pemesanan aktif atau yang akan datang untuk kamar ini.</p>
            @endforelse
        </div>

        <details class="room-calendar-collapse">
            <summary>
                <span>Lihat Kalender Pemesanan</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </summary>

            <div class="room-calendar__legend">
                <span><i class="is-tentative"></i> Sudah keluar / menunggu konfirmasi</span>
                <span><i class="is-blocked"></i> Sudah final / belum keluar</span>
            </div>

            <div class="room-calendar">
                @foreach ($bookingCalendar as $month)
                    <div class="room-calendar__month">
                        <h3>{{ $month['label'] }}</h3>
                        <div class="room-calendar__weekdays" aria-hidden="true">
                            <span>Sen</span>
                            <span>Sel</span>
                            <span>Rab</span>
                            <span>Kam</span>
                            <span>Jum</span>
                            <span>Sab</span>
                            <span>Min</span>
                        </div>
                        <div class="room-calendar__days">
                            @foreach ($month['days'] as $day)
                                <span
                                    class="room-calendar__day room-calendar__day--{{ $day['state'] }} {{ $day['in_month'] ? '' : 'is-outside' }}"
                                    title="{{ $day['title'] ?? 'Tersedia' }}"
                                >
                                    {{ $day['number'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </details>
    </section>
</div>
