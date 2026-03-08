-- ============================================================
-- DATABASE DESIGN: Sistem Manajemen Kost & Guesthouse Hybrid
-- Politeknik Negeri Malang - Kelompok 1 TI-2E
-- PostgreSQL Version
-- ============================================================

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- ENUM TYPES
-- ============================================================

CREATE TYPE user_role       AS ENUM ('admin', 'penyewa');
CREATE TYPE stay_type       AS ENUM ('harian', 'bulanan');
CREATE TYPE room_status     AS ENUM ('tersedia', 'terisi', 'perbaikan');
CREATE TYPE booking_status  AS ENUM ('pending', 'menunggu_konfirmasi', 'active_stay', 'selesai', 'dibatalkan');
CREATE TYPE payment_status  AS ENUM ('menunggu_verifikasi', 'terverifikasi', 'ditolak');
CREATE TYPE payment_method  AS ENUM ('transfer_bank', 'qris', 'tunai');

-- ============================================================
-- TABLE: users
-- ============================================================

CREATE TABLE users (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nama_lengkap    VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    no_telepon      VARCHAR(20)  NOT NULL UNIQUE,
    no_ktp          VARCHAR(20),
    role            user_role    NOT NULL DEFAULT 'penyewa',
    is_active       BOOLEAN      NOT NULL DEFAULT TRUE,
    remember_token  VARCHAR(100),
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE  users           IS 'Pengguna sistem: admin dan penyewa';
COMMENT ON COLUMN users.no_ktp    IS 'Nomor KTP untuk identifikasi penyewa';

-- ============================================================
-- TABLE: rooms
-- Satu tipe kamar, semua kamar mandi dalam
-- ============================================================

CREATE TABLE rooms (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nomor_kamar     VARCHAR(10)    NOT NULL UNIQUE,
    lantai          SMALLINT       NOT NULL DEFAULT 1,
    luas_m2         NUMERIC(5,2),
    deskripsi       TEXT,
    fasilitas       TEXT[],        -- array teks: ['AC', 'WiFi', 'Water Heater', ...]
    harga_harian    NUMERIC(12,2)  NOT NULL,
    harga_bulanan   NUMERIC(12,2)  NOT NULL,
    deposit         NUMERIC(12,2)  NOT NULL DEFAULT 0,
    status          room_status    NOT NULL DEFAULT 'tersedia',
    foto_utama      VARCHAR(255),
    is_published    BOOLEAN        NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ    NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ    NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE  rooms            IS 'Data kamar. Semua tipe standard dengan kamar mandi dalam';
COMMENT ON COLUMN rooms.fasilitas  IS 'Daftar fasilitas kamar dalam bentuk array teks';
COMMENT ON COLUMN rooms.deposit    IS 'Uang jaminan yang dibayar di awal sewa';

-- ============================================================
-- TABLE: bookings
-- ============================================================

CREATE TABLE bookings (
    id                  UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    kode_booking        VARCHAR(20)    NOT NULL UNIQUE,
    user_id             UUID           NOT NULL REFERENCES users(id),
    room_id             UUID           NOT NULL REFERENCES rooms(id),
    tipe_sewa           stay_type      NOT NULL,
    tanggal_check_in    DATE           NOT NULL,
    tanggal_check_out   DATE           NOT NULL,
    durasi              INT            NOT NULL, -- hari atau bulan
    harga_snapshot      NUMERIC(12,2)  NOT NULL, -- harga saat booking dibuat
    total_tagihan       NUMERIC(12,2)  NOT NULL,
    catatan_penyewa     TEXT,
    status              booking_status NOT NULL DEFAULT 'pending',
    alasan_pembatalan   TEXT,
    dikonfirmasi_oleh   UUID           REFERENCES users(id),
    tanggal_konfirmasi  TIMESTAMPTZ,
    created_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_checkout_setelah_checkin CHECK (tanggal_check_out > tanggal_check_in),
    CONSTRAINT chk_durasi_positif           CHECK (durasi > 0),
    CONSTRAINT chk_tagihan_positif          CHECK (total_tagihan > 0)
);

COMMENT ON TABLE  bookings               IS 'Pemesanan kamar oleh penyewa';
COMMENT ON COLUMN bookings.harga_snapshot IS 'Snapshot harga per unit saat booking, tidak berubah meski harga kamar diperbarui';

CREATE INDEX idx_bookings_user_id     ON bookings(user_id);
CREATE INDEX idx_bookings_room_id     ON bookings(room_id);
CREATE INDEX idx_bookings_status      ON bookings(status);
CREATE INDEX idx_bookings_tanggal     ON bookings(tanggal_check_in, tanggal_check_out);

-- Index khusus untuk cek konflik jadwal (partial index, hanya status aktif)
CREATE INDEX idx_bookings_room_aktif  ON bookings(room_id, tanggal_check_in, tanggal_check_out)
    WHERE status IN ('pending', 'menunggu_konfirmasi', 'active_stay');

-- ============================================================
-- TABLE: payments
-- Pembayaran via WhatsApp, admin verifikasi manual
-- ============================================================

CREATE TABLE payments (
    id                  UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    kode_pembayaran     VARCHAR(25)    NOT NULL UNIQUE,
    booking_id          UUID           NOT NULL REFERENCES bookings(id),
    user_id             UUID           NOT NULL REFERENCES users(id),
    jumlah              NUMERIC(12,2)  NOT NULL,
    metode_pembayaran   payment_method NOT NULL,
    nama_bank           VARCHAR(50),
    nama_pengirim       VARCHAR(100),
    bukti_pembayaran    VARCHAR(255),  -- path file foto bukti
    tanggal_transfer    DATE,
    status              payment_status NOT NULL DEFAULT 'menunggu_verifikasi',
    diverifikasi_oleh   UUID           REFERENCES users(id),
    tanggal_verifikasi  TIMESTAMPTZ,
    alasan_penolakan    TEXT,
    created_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_jumlah_positif CHECK (jumlah > 0)
);

COMMENT ON TABLE  payments                  IS 'Pembayaran via WhatsApp. Penyewa upload bukti, admin verifikasi';
COMMENT ON COLUMN payments.bukti_pembayaran IS 'Path file foto bukti transfer yang diunggah penyewa';

CREATE INDEX idx_payments_booking_id ON payments(booking_id);
CREATE INDEX idx_payments_status     ON payments(status);

-- ============================================================
-- TABLE: notifications
-- ============================================================

CREATE TABLE notifications (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id     UUID        NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    judul       VARCHAR(150) NOT NULL,
    pesan       TEXT         NOT NULL,
    is_read     BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_notif_user_unread ON notifications(user_id, is_read, created_at DESC);

-- ============================================================
-- TABLE: system_settings
-- ============================================================

CREATE TABLE system_settings (
    key         VARCHAR(100) PRIMARY KEY,
    value       TEXT         NOT NULL,
    deskripsi   TEXT,
    updated_at  TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

INSERT INTO system_settings (key, value, deskripsi) VALUES
    ('wa_admin_number',         '6281234567890',                    'Nomor WhatsApp admin untuk konfirmasi pembayaran'),
    ('batas_waktu_pembayaran',  '24',                               'Batas waktu upload bukti pembayaran (jam)'),
    ('reminder_checkout_jam',   '24',                               'Jam sebelum checkout untuk kirim reminder'),
    ('nama_bisnis',             'Kost & Guesthouse Hybrid',         'Nama bisnis'),
    ('alamat_bisnis',           'Jl. Soekarno Hatta No. 9, Malang', 'Alamat bisnis');

-- ============================================================
-- VIEWS
-- ============================================================

-- View: Booking lengkap (join penyewa + kamar + pembayaran terakhir)
CREATE OR REPLACE VIEW v_bookings_full AS
SELECT
    b.id,
    b.kode_booking,
    b.tipe_sewa,
    b.tanggal_check_in,
    b.tanggal_check_out,
    b.durasi,
    b.total_tagihan,
    b.status              AS booking_status,
    b.catatan_penyewa,
    b.created_at,
    -- Sisa hari (hanya untuk active_stay)
    CASE WHEN b.status = 'active_stay'
         THEN (b.tanggal_check_out - CURRENT_DATE) END  AS sisa_hari,
    -- Penyewa
    u.nama_lengkap        AS nama_penyewa,
    u.no_telepon          AS telepon_penyewa,
    u.email               AS email_penyewa,
    -- Kamar
    r.nomor_kamar,
    r.lantai,
    r.harga_harian,
    r.harga_bulanan,
    -- Pembayaran terakhir
    p.id                  AS payment_id,
    p.kode_pembayaran,
    p.jumlah              AS jumlah_bayar,
    p.bukti_pembayaran,
    p.status              AS payment_status,
    p.tanggal_verifikasi
FROM bookings b
JOIN users u ON u.id = b.user_id
JOIN rooms r ON r.id = b.room_id
LEFT JOIN LATERAL (
    SELECT * FROM payments
    WHERE booking_id = b.id
    ORDER BY created_at DESC
    LIMIT 1
) p ON TRUE;

COMMENT ON VIEW v_bookings_full IS 'Detail booking lengkap beserta data penyewa, kamar, dan pembayaran terakhir';

-- ============================================================
-- View: Ketersediaan kamar hari ini
CREATE OR REPLACE VIEW v_room_availability AS
SELECT
    r.id,
    r.nomor_kamar,
    r.lantai,
    r.harga_harian,
    r.harga_bulanan,
    r.status              AS status_kamar,
    -- Booking yang sedang aktif hari ini
    b.id                  AS booking_aktif_id,
    b.tipe_sewa,
    b.tanggal_check_in,
    b.tanggal_check_out,
    u.nama_lengkap        AS nama_penyewa_aktif,
    u.no_telepon          AS telepon_penyewa_aktif
FROM rooms r
LEFT JOIN bookings b
    ON  b.room_id = r.id
    AND b.status IN ('active_stay', 'menunggu_konfirmasi')
    AND CURRENT_DATE BETWEEN b.tanggal_check_in AND b.tanggal_check_out
LEFT JOIN users u ON u.id = b.user_id
WHERE r.is_published = TRUE;

COMMENT ON VIEW v_room_availability IS 'Status ketersediaan seluruh kamar pada hari ini';

-- ============================================================
-- View: Ringkasan dashboard admin
CREATE OR REPLACE VIEW v_dashboard_stats AS
SELECT
    (SELECT COUNT(*)   FROM rooms   WHERE status = 'tersedia')                          AS kamar_tersedia,
    (SELECT COUNT(*)   FROM rooms   WHERE status = 'terisi')                            AS kamar_terisi,
    (SELECT COUNT(*)   FROM rooms   WHERE status = 'perbaikan')                         AS kamar_perbaikan,
    (SELECT COUNT(*)   FROM bookings WHERE status = 'menunggu_konfirmasi')              AS booking_perlu_konfirmasi,
    (SELECT COUNT(*)   FROM payments WHERE status = 'menunggu_verifikasi')              AS pembayaran_perlu_verifikasi,
    (SELECT COUNT(*)   FROM bookings WHERE status = 'active_stay'
                                      AND tanggal_check_out = CURRENT_DATE)             AS checkout_hari_ini,
    (SELECT COALESCE(SUM(jumlah), 0) FROM payments
     WHERE  status = 'terverifikasi'
       AND  DATE_TRUNC('month', tanggal_verifikasi) = DATE_TRUNC('month', NOW()))       AS pendapatan_bulan_ini,
    NOW()                                                                               AS diperbarui_pada;

COMMENT ON VIEW v_dashboard_stats IS 'Statistik ringkas untuk halaman dashboard admin';

-- ============================================================
-- MATERIALIZED VIEW: Laporan pendapatan bulanan
-- ============================================================

CREATE MATERIALIZED VIEW mv_laporan_bulanan AS
SELECT
    DATE_TRUNC('month', p.tanggal_verifikasi)::DATE  AS bulan,
    TO_CHAR(p.tanggal_verifikasi, 'Month YYYY')      AS label_bulan,
    COUNT(DISTINCT p.id)                              AS jumlah_transaksi,
    COUNT(DISTINCT b.id)                              AS jumlah_booking,
    SUM(CASE WHEN b.tipe_sewa = 'harian'  THEN p.jumlah ELSE 0 END) AS pendapatan_harian,
    SUM(CASE WHEN b.tipe_sewa = 'bulanan' THEN p.jumlah ELSE 0 END) AS pendapatan_bulanan,
    COALESCE(SUM(p.jumlah), 0)                        AS total_pendapatan
FROM payments p
JOIN bookings b ON b.id = p.booking_id
WHERE p.status = 'terverifikasi'
GROUP BY DATE_TRUNC('month', p.tanggal_verifikasi),
         TO_CHAR(p.tanggal_verifikasi, 'Month YYYY')
ORDER BY bulan DESC;

CREATE UNIQUE INDEX idx_mv_laporan_bulan ON mv_laporan_bulanan(bulan);

COMMENT ON MATERIALIZED VIEW mv_laporan_bulanan IS
    'Rekapitulasi pendapatan per bulan. Refresh dengan: REFRESH MATERIALIZED VIEW CONCURRENTLY mv_laporan_bulanan';

-- ============================================================
-- FUNCTIONS
-- ============================================================

-- Function: Cek ketersediaan kamar untuk rentang tanggal
CREATE OR REPLACE FUNCTION fn_cek_ketersediaan(
    p_room_id        UUID,
    p_check_in       DATE,
    p_check_out      DATE,
    p_exclude_id     UUID DEFAULT NULL
)
RETURNS BOOLEAN AS $$
DECLARE
    v_konflik INT;
    v_status  room_status;
BEGIN
    SELECT status INTO v_status FROM rooms WHERE id = p_room_id;

    IF v_status IN ('perbaikan') THEN
        RETURN FALSE;
    END IF;

    SELECT COUNT(*) INTO v_konflik
    FROM bookings
    WHERE room_id = p_room_id
      AND status IN ('pending', 'menunggu_konfirmasi', 'active_stay')
      AND (p_exclude_id IS NULL OR id != p_exclude_id)
      AND NOT (tanggal_check_out <= p_check_in OR tanggal_check_in >= p_check_out);

    RETURN v_konflik = 0;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION fn_cek_ketersediaan IS
    'Mengembalikan TRUE jika kamar tersedia untuk rentang tanggal yang diberikan';

-- ============================================================
-- Function: Laporan pendapatan per rentang tanggal
CREATE OR REPLACE FUNCTION fn_laporan_pendapatan(
    p_dari   DATE,
    p_sampai DATE
)
RETURNS TABLE (
    tanggal          DATE,
    jumlah_transaksi BIGINT,
    total_pendapatan NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        DATE(p.tanggal_verifikasi),
        COUNT(p.id),
        COALESCE(SUM(p.jumlah), 0)
    FROM payments p
    WHERE p.status = 'terverifikasi'
      AND DATE(p.tanggal_verifikasi) BETWEEN p_dari AND p_sampai
    GROUP BY DATE(p.tanggal_verifikasi)
    ORDER BY DATE(p.tanggal_verifikasi);
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION fn_laporan_pendapatan IS
    'Laporan pendapatan harian dalam rentang tanggal. Contoh: SELECT * FROM fn_laporan_pendapatan(''2026-01-01'', ''2026-01-31'')';

-- ============================================================
-- TRIGGERS
-- ============================================================

-- Trigger: Auto-update kolom updated_at
CREATE OR REPLACE FUNCTION fn_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_users_updated_at
    BEFORE UPDATE ON users    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();
CREATE TRIGGER trg_rooms_updated_at
    BEFORE UPDATE ON rooms    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();
CREATE TRIGGER trg_bookings_updated_at
    BEFORE UPDATE ON bookings FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();
CREATE TRIGGER trg_payments_updated_at
    BEFORE UPDATE ON payments FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

-- ============================================================
-- Trigger: Saat pembayaran terverifikasi → ubah status booking + status kamar
CREATE OR REPLACE FUNCTION fn_on_payment_verified()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.status = 'terverifikasi' AND OLD.status != 'terverifikasi' THEN
        -- Ubah booking menjadi active_stay
        UPDATE bookings
        SET status             = 'active_stay',
            tanggal_konfirmasi = NOW(),
            dikonfirmasi_oleh  = NEW.diverifikasi_oleh,
            updated_at         = NOW()
        WHERE id = NEW.booking_id;

        -- Ubah status kamar menjadi terisi
        UPDATE rooms
        SET status     = 'terisi',
            updated_at = NOW()
        WHERE id = (SELECT room_id FROM bookings WHERE id = NEW.booking_id);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_payment_verified
    AFTER UPDATE ON payments
    FOR EACH ROW EXECUTE FUNCTION fn_on_payment_verified();

COMMENT ON FUNCTION fn_on_payment_verified IS
    'Otomatis mengubah status booking menjadi active_stay dan status kamar menjadi terisi saat pembayaran diverifikasi admin';

-- ============================================================
-- Trigger: Cegah double booking
CREATE OR REPLACE FUNCTION fn_check_double_booking()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT fn_cek_ketersediaan(NEW.room_id, NEW.tanggal_check_in, NEW.tanggal_check_out, NEW.id) THEN
        RAISE EXCEPTION 'Kamar % sudah dipesan untuk tanggal % s/d %',
            (SELECT nomor_kamar FROM rooms WHERE id = NEW.room_id),
            NEW.tanggal_check_in,
            NEW.tanggal_check_out;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_no_double_booking
    BEFORE INSERT OR UPDATE ON bookings
    FOR EACH ROW
    WHEN (NEW.status NOT IN ('dibatalkan', 'selesai'))
    EXECUTE FUNCTION fn_check_double_booking();

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin
INSERT INTO users (nama_lengkap, email, password_hash, no_telepon, role) VALUES
    ('Admin Utama', 'admin@kost.id', '$2y$12$placeholder_hash', '081234567890', 'admin');

-- Kamar (semua kamar mandi dalam, fasilitas disimpan sebagai array)
INSERT INTO rooms (nomor_kamar, lantai, luas_m2, deskripsi, fasilitas, harga_harian, harga_bulanan, deposit) VALUES
    ('101', 1, 16, 'Kamar lantai 1, dekat parkiran',          ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Parkir Motor'],                        150000, 1500000, 500000),
    ('102', 1, 16, 'Kamar lantai 1, jendela menghadap taman', ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Parkir Motor'],                        150000, 1500000, 500000),
    ('103', 1, 18, 'Kamar lantai 1, lebih luas',              ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'Parkir Motor'],        175000, 1750000, 500000),
    ('201', 2, 16, 'Kamar lantai 2, view jalan utama',        ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'Parkir Motor'],        160000, 1600000, 500000),
    ('202', 2, 16, 'Kamar lantai 2, sudut, lebih privat',     ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'Parkir Motor'],        160000, 1600000, 500000),
    ('203', 2, 18, 'Kamar lantai 2, ada balkon kecil',        ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'TV LED', 'Parkir Motor'], 185000, 1850000, 500000),
    ('301', 3, 20, 'Kamar lantai 3, terluas, view terbaik',   ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'TV LED', 'Kulkas Mini', 'Parkir Motor'], 200000, 2000000, 750000),
    ('302', 3, 20, 'Kamar lantai 3, tenang dan sejuk',        ARRAY['AC', 'WiFi', 'Lemari', 'Meja Belajar', 'Kasur Spring Bed', 'Water Heater', 'TV LED', 'Kulkas Mini', 'Parkir Motor'], 200000, 2000000, 750000);

-- ============================================================
-- END OF SCHEMA
-- ============================================================

-- ============================================================
-- TABLE: payments
-- Deskripsi: Mencatat data verifikasi pembayaran yang dilakukan secara manual via WhatsApp.
-- Perubahan: Menghapus user_id (redundansi) karena sudah terhubung via booking_id. [cite: 56, 130]
-- ============================================================

DROP TABLE IF EXISTS payments CASCADE;

CREATE TABLE payments (
    id                  UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    kode_pembayaran     VARCHAR(25)    NOT NULL UNIQUE,
    booking_id          UUID           NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    
    -- Data Nominal & Metode
    jumlah              NUMERIC(12,2)  NOT NULL,
    metode_pembayaran   payment_method NOT NULL DEFAULT 'transfer_bank',
    
    -- Detail Pengirim (Diisi Admin berdasarkan data dari chat WA)
    nama_bank           VARCHAR(50),   -- Contoh: BCA, Mandiri, dll.
    nama_pengirim       VARCHAR(100),  -- Nama yang tertera di rekening pengirim.
    tanggal_transfer    DATE,          -- Tanggal transfer dilakukan oleh penyewa.
    
    -- Bukti Digital (Opsi: Admin mengunggah screenshot dari WA ke sistem)
    bukti_pembayaran    VARCHAR(255),  -- Path file gambar bukti transfer.
    
    -- Log Verifikasi Admin (Proses Konfirmasi di Website)
    status              payment_status NOT NULL DEFAULT 'menunggu_verifikasi',
    diverifikasi_oleh   UUID           REFERENCES users(id), -- ID Admin yang menekan tombol konfirmasi. [cite: 51, 53]
    tanggal_verifikasi  TIMESTAMPTZ,   -- Waktu otomatis saat admin melakukan konfirmasi di web.
    alasan_penolakan    TEXT,          -- Diisi jika admin menolak bukti pembayaran (misal: dana belum masuk).
    
    -- Timestamps
    created_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ    NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_jumlah_positif CHECK (jumlah > 0)
);

-- Dokumentasi Kolom
COMMENT ON TABLE  payments                  IS 'Log verifikasi pembayaran manual. Admin memverifikasi via WA, lalu input data ke sistem.';
COMMENT ON COLUMN payments.diverifikasi_oleh IS 'ID Admin yang melakukan verifikasi bukti pembayaran di dashboard.';

-- Index untuk mempercepat pencarian status dan relasi booking
CREATE INDEX idx_payments_booking_id ON payments(booking_id);
CREATE INDEX idx_payments_status     ON payments(status);