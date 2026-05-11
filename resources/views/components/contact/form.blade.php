<form
    class="contact-form"
    id="guest-request-form"
    method="POST"
    action="{{ route('contact.store') }}"
>
    @csrf

    {{-- SUCCESS MESSAGE --}}
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

            <h3>Permintaan Berhasil Dibuat! 🎉</h3>

            <p>
                {{ session('success') }}
            </p>
        </div>
    @endif

    {{-- ─── INFORMASI DASAR ───────────────────────── --}}
    <div class="form-section-title">
        👤 Informasi Pemesan
    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Nama Lengkap *</label>

            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Budi Santoso"
                required
            />
        </div>

        <div class="form-group">
            <label>No. WhatsApp / HP *</label>

            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="+62 812-xxxx-xxxx"
                required
            />
        </div>

    </div>

    <div class="form-group">
        <label>Email</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="email@kamu.com"
        />
    </div>

    {{-- ─── JENIS REQUEST ───────────────────────── --}}
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

            <option value="availability">
                Cek Ketersediaan Kamar
            </option>

            <option value="booking">
                Booking Kamar
            </option>

            <option value="reschedule">
                Perubahan Jadwal Menginap
            </option>

            <option value="cancel">
                Pembatalan Booking
            </option>

            <option value="complaint">
                Keluhan / Bantuan
            </option>

            <option value="general">
                Pertanyaan Umum
            </option>
        </select>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- BOOKING --}}
    {{-- ───────────────────────────────────────────── --}}
    <div
        class="dynamic-section"
        id="section-booking"
        style="display:none;"
    >

        <div class="form-divider-label">
            🛏 Detail Menginap
        </div>

        <div class="form-divider"></div>

        <div class="form-row">

            <div class="form-group">
                <label>Check-in</label>

                <input
                    type="date"
                    id="checkin"
                    name="checkin"
                />
            </div>

            <div class="form-group">
                <label>Check-out</label>

                <input
                    type="date"
                    id="checkout"
                    name="checkout"
                />
            </div>

        </div>

        <div class="form-row">

            <div class="form-group">

                <label>Tipe Kamar</label>

                <select
                    id="tipe_kamar"
                    name="tipe_kamar"
                >

                    <option value="">
                        — Pilih Kamar —
                    </option>

                    @foreach($rooms->unique('tipe_kamar') as $room)
                        <option value="{{ $room->tipe_kamar }}">
                            {{ $room->tipe_kamar }}
                        </option>
                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>Tipe Sewa</label>

                <select
                    id="tipe_sewa"
                    name="tipe_sewa"
                >
                    <option value="">
                        — Pilih —
                    </option>

                    <option value="harian">
                        Harian
                    </option>

                    <option value="bulanan">
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

                <option>
                    1 orang
                </option>

                <option>
                    2 orang
                </option>

                <option>
                    3–5 orang
                </option>

                <option>
                    5+ orang
                </option>
            </select>

        </div>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- RESCHEDULE / CANCEL --}}
    {{-- ───────────────────────────────────────────── --}}
    <div
        class="dynamic-section"
        id="section-booking-management"
        style="display:none;"
    >

        <div class="form-divider-label">
            📅 Pengelolaan Booking
        </div>

        <div class="form-divider"></div>

        <div class="form-group">

            <label>Alasan</label>

            <textarea
                id="manage_reason"
                name="manage_reason"
                placeholder="Tuliskan alasan perubahan atau pembatalan..."
            ></textarea>

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
            ⚠ Keluhan / Bantuan
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

                <option>
                    Kamar
                </option>

                <option>
                    Kebersihan
                </option>

                <option>
                    Pembayaran
                </option>

                <option>
                    Fasilitas
                </option>

                <option>
                    Staff
                </option>

                <option>
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
            placeholder="Tulis kebutuhan atau pertanyaan kamu..."
        ></textarea>

    </div>

    {{-- ───────────────────────────────────────────── --}}
    {{-- SUBMIT --}}
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

    function hideAllSections() {
        Object.values(sections).forEach(section => {
            if (section) {
                section.style.display = 'none';
            }
        });
    }

    requestType.addEventListener('change', () => {

        hideAllSections();

        const value = requestType.value;

        if ((value === 'availability' || value === 'booking') && sections.booking) {
            sections.booking.style.display = 'flex';
        }

        if ((value === 'reschedule' || value === 'cancel') && sections.management) {
            sections.management.style.display = 'flex';
        }

        if (value === 'complaint' && sections.complaint) {
            sections.complaint.style.display = 'flex';
        }
    });

});
</script>
