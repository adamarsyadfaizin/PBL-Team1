<form
    class="contact-form"
    id="guest-request-form"
    method="POST"
    action="{{ route('contact.store') }}"
>
    @csrf

    {{-- PESAN BERHASIL --}}
    @if(session('success'))
        <div class="form-success">

            <div class="success-icon">
                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>

            <h3>Permintaan Berhasil Dibuat</h3>

            <p>
                {{ session('success') }}
            </p>
        </div>
    @endif

    @if($errors->any())
        <div class="form-success">
            <h3>Permintaan belum bisa dikirim</h3>
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- ─── INFORMASI DASAR ───────────────────────── --}}
    <div class="form-section-title">
        Informasi Pemesan
    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Nama Lengkap *</label>

            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Budi Santoso"
                value="{{ old('nama') }}"
                required
            />
        </div>

        <div class="form-group">
        <label>No. WhatsApp / Ponsel *</label>

            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="+62 812-xxxx-xxxx"
                value="{{ old('phone') }}"
                required
            />
        </div>

    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Post-el</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="contoh@postel.com"
                value="{{ old('email') }}"
            />
        </div>

            {{-- ─── JENIS PERMINTAAN ───────────────────────── --}}
        <div class="form-group">

            <label>Jenis Permintaan *</label>

            <select
                id="request_type"
                name="request_type"
                required
            >
                <option value="">
                    — Pilih Jenis Permintaan —
                </option>

                <option value="availability" @selected(old('request_type') === 'availability')>
                    Cek Ketersediaan Kamar
                </option>

                <option value="booking" @selected(old('request_type') === 'booking')>
                    Pemesanan Kamar
                </option>

                <option value="reschedule" @selected(old('request_type') === 'reschedule')>
                    Perubahan Jadwal Menginap
                </option>

                <option value="cancel" @selected(old('request_type') === 'cancel')>
                    Pembatalan Pemesanan
                </option>

                <option value="complaint" @selected(old('request_type') === 'complaint')>
                    Keluhan / Bantuan
                </option>

                <option value="general" @selected(old('request_type') === 'general')>
                    Pertanyaan Umum
                </option>
            </select>

        </div>
    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- PEMESANAN --}}
    {{-- ───────────────────────────────────────────── --}}
    <div
        class="dynamic-section"
        id="section-booking"
        style="display:none;"
    >

        <div class="form-divider-label">
            Detail Menginap
        </div>

        <div class="form-divider"></div>

        <div class="form-row">

            <div class="form-group">
                <label>Tanggal masuk</label>

                <input
                    type="date"
                    id="checkin"
                    name="checkin"
                    value="{{ old('checkin') }}"
                    data-stay-required
                />
            </div>

            <div class="form-group">
                <label>Tanggal keluar</label>

                <input
                    type="date"
                    id="checkout"
                    name="checkout"
                    value="{{ old('checkout') }}"
                    data-stay-required
                />
            </div>

        </div>

        <div class="form-row">

            <div class="form-group">

                <label>Pilih Kamar</label>

                <select
                    id="tipe_kamar"
                    name="tipe_kamar"
                    data-stay-required
                >

                    <option value="">
                        — Pilih Kamar —
                    </option>

                    @forelse($rooms as $room)
                        @php
                            $roomStatus = (string) ($room->status->value ?? $room->status);
                            $statusLabel = match ($roomStatus) {
                                'tersedia' => 'Tersedia',
                                'terisi' => 'Terisi',
                                'perbaikan' => 'Perbaikan',
                                default => ucfirst($roomStatus),
                            };
                            $dailyPrice = 'Rp ' . number_format((float) $room->harga_harian, 0, ',', '.') . '/malam';
                        @endphp
                        <option value="{{ $room->nomor_kamar }}" @selected(old('tipe_kamar') === $room->nomor_kamar)>
                            Kamar {{ $room->nomor_kamar }} - Lantai {{ $room->lantai }} - {{ $statusLabel }} - {{ $dailyPrice }}
                        </option>
                    @empty
                        <option value="" disabled>
                            Belum ada kamar yang bisa dipilih
                        </option>
                    @endforelse

                </select>

            </div>

            <div class="form-group">

                <label>Tipe Sewa</label>

                <select
                    id="tipe_sewa"
                    name="tipe_sewa"
                    data-stay-required
                >
                    <option value="">
                        — Pilih —
                    </option>

                    <option value="harian" @selected(old('tipe_sewa') === 'harian')>
                        Harian
                    </option>

                    <option value="bulanan" @selected(old('tipe_sewa') === 'bulanan')>
                        Bulanan
                    </option>
                </select>

            </div>

        </div>

        <div class="form-group">

            <label>Jumlah Tamu</label>

            <select
                id="jumlah_tamu"
                name="jumlah_tamu"
            >
                <option value="">
                    — Pilih —
                </option>

                <option value="1 orang" @selected(old('jumlah_tamu') === '1 orang')>
                    1 orang
                </option>

                <option value="2 orang" @selected(old('jumlah_tamu') === '2 orang')>
                    2 orang
                </option>

                <option value="3-5 orang" @selected(old('jumlah_tamu') === '3-5 orang')>
                    3–5 orang
                </option>

                <option value="5+ orang" @selected(old('jumlah_tamu') === '5+ orang')>
                    5+ orang
                </option>
            </select>

        </div>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- PERUBAHAN JADWAL / PEMBATALAN --}}
    {{-- ───────────────────────────────────────────── --}}
    <div
        class="dynamic-section"
        id="section-booking-management"
        style="display:none;"
    >

        <div class="form-divider-label">
            Pengelolaan Pemesanan
        </div>

        <div class="form-divider"></div>

        <div class="form-group">

            <label>Alasan</label>

            <textarea
                id="manage_reason"
                name="manage_reason"
                placeholder="Tuliskan alasan perubahan atau pembatalan..."
            >{{ old('manage_reason') }}</textarea>

        </div>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- COMPLAINT --}}
    {{-- ───────────────────────────────────────────── --}}
    <div
        class="dynamic-section"
        id="section-complaint"
        style="display:none;"
    >

        <div class="form-divider-label">
            Keluhan / Bantuan
        </div>

        <div class="form-divider"></div>

        <div class="form-group">

            <label>Kategori Keluhan</label>

            <select
                id="complaint_category"
                name="complaint_category"
            >

                <option value="">
                    — Pilih —
                </option>

                <option value="Kamar" @selected(old('complaint_category') === 'Kamar')>
                    Kamar
                </option>

                <option value="Kebersihan" @selected(old('complaint_category') === 'Kebersihan')>
                    Kebersihan
                </option>

                <option value="Pembayaran" @selected(old('complaint_category') === 'Pembayaran')>
                    Pembayaran
                </option>

                <option value="Fasilitas" @selected(old('complaint_category') === 'Fasilitas')>
                    Fasilitas
                </option>

                <option value="Staf" @selected(old('complaint_category') === 'Staf')>
                    Staf
                </option>

                <option value="Lainnya" @selected(old('complaint_category') === 'Lainnya')>
                    Lainnya
                </option>

            </select>

        </div>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- PESAN --}}
    {{-- ───────────────────────────────────────────── --}}
    <div class="form-group">

        <label>Pesan / Catatan</label>

        <textarea
            id="pesan"
            name="pesan"
            placeholder="Tulis kebutuhan atau pertanyaan Anda..."
        >{{ old('pesan') }}</textarea>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- KIRIM --}}
    {{-- ───────────────────────────────────────────── --}}
    <button type="submit" class="btn-submit">

        <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>

        Kirim Permintaan

    </button>

</form>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const requestType = document.getElementById('request_type');

    const sections = {
        booking: document.getElementById('section-booking'),
        management: document.getElementById('section-booking-management'),
        complaint: document.getElementById('section-complaint'),
    };

    const stayRequiredFields = document.querySelectorAll('[data-stay-required]');

    function hideAllSections() {
        Object.values(sections).forEach(section => {
            if (section) {
                section.style.display = 'none';
            }
        });
    }

    function updateSections() {
        hideAllSections();

        const value = requestType.value;
        const needsStayDetails = value === 'availability' || value === 'booking';

        stayRequiredFields.forEach(field => {
            field.required = needsStayDetails;
        });

        if (needsStayDetails && sections.booking) {
            sections.booking.style.display = 'flex';
        }

        if ((value === 'reschedule' || value === 'cancel') && sections.management) {
            sections.management.style.display = 'flex';
        }

        if (value === 'complaint' && sections.complaint) {
            sections.complaint.style.display = 'flex';
        }
    }

    requestType.addEventListener('change', updateSections);
    updateSections();

});
</script>
