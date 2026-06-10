--
-- PostgreSQL database dump
--

\restrict 0rWq7EmSscWRBM7iJstLjZE72msuoUNeWrLw72XGbFPh2O66wh7hwHvQjAH2iLU

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2026-06-10 11:17:17

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 67804)
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- TOC entry 3555 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- TOC entry 883 (class 1247 OID 67836)
-- Name: booking_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.booking_status AS ENUM (
    'pending',
    'menunggu_konfirmasi',
    'active_stay',
    'selesai',
    'dibatalkan'
);


ALTER TYPE public.booking_status OWNER TO postgres;

--
-- TOC entry 889 (class 1247 OID 67856)
-- Name: payment_method; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.payment_method AS ENUM (
    'transfer_bank',
    'qris',
    'tunai'
);


ALTER TYPE public.payment_method OWNER TO postgres;

--
-- TOC entry 886 (class 1247 OID 67848)
-- Name: payment_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.payment_status AS ENUM (
    'menunggu_verifikasi',
    'terverifikasi',
    'ditolak'
);


ALTER TYPE public.payment_status OWNER TO postgres;

--
-- TOC entry 880 (class 1247 OID 67828)
-- Name: room_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.room_status AS ENUM (
    'tersedia',
    'terisi',
    'perbaikan'
);


ALTER TYPE public.room_status OWNER TO postgres;

--
-- TOC entry 877 (class 1247 OID 67822)
-- Name: stay_type; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.stay_type AS ENUM (
    'harian',
    'bulanan'
);


ALTER TYPE public.stay_type OWNER TO postgres;

--
-- TOC entry 874 (class 1247 OID 67816)
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.user_role AS ENUM (
    'admin',
    'penyewa'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- TOC entry 247 (class 1255 OID 68009)
-- Name: fn_cek_ketersediaan(uuid, date, date, uuid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_cek_ketersediaan(p_room_id uuid, p_check_in date, p_check_out date, p_exclude_id uuid DEFAULT NULL::uuid) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.fn_cek_ketersediaan(p_room_id uuid, p_check_in date, p_check_out date, p_exclude_id uuid) OWNER TO postgres;

--
-- TOC entry 3556 (class 0 OID 0)
-- Dependencies: 247
-- Name: FUNCTION fn_cek_ketersediaan(p_room_id uuid, p_check_in date, p_check_out date, p_exclude_id uuid); Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON FUNCTION public.fn_cek_ketersediaan(p_room_id uuid, p_check_in date, p_check_out date, p_exclude_id uuid) IS 'Mengembalikan TRUE jika kamar tersedia untuk rentang tanggal yang diberikan';


--
-- TOC entry 251 (class 1255 OID 68018)
-- Name: fn_check_double_booking(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_check_double_booking() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF NOT fn_cek_ketersediaan(NEW.room_id, NEW.tanggal_check_in, NEW.tanggal_check_out, NEW.id) THEN
        RAISE EXCEPTION 'Kamar % sudah dipesan untuk tanggal % s/d %',
            (SELECT nomor_kamar FROM rooms WHERE id = NEW.room_id),
            NEW.tanggal_check_in,
            NEW.tanggal_check_out;
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_check_double_booking() OWNER TO postgres;

--
-- TOC entry 248 (class 1255 OID 68010)
-- Name: fn_laporan_pendapatan(date, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_laporan_pendapatan(p_dari date, p_sampai date) RETURNS TABLE(tanggal date, jumlah_transaksi bigint, total_pendapatan numeric)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.fn_laporan_pendapatan(p_dari date, p_sampai date) OWNER TO postgres;

--
-- TOC entry 3557 (class 0 OID 0)
-- Dependencies: 248
-- Name: FUNCTION fn_laporan_pendapatan(p_dari date, p_sampai date); Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON FUNCTION public.fn_laporan_pendapatan(p_dari date, p_sampai date) IS 'Laporan pendapatan harian dalam rentang tanggal. Contoh: SELECT * FROM fn_laporan_pendapatan(''2026-01-01'', ''2026-01-31'')';


--
-- TOC entry 250 (class 1255 OID 68016)
-- Name: fn_on_payment_verified(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_on_payment_verified() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.fn_on_payment_verified() OWNER TO postgres;

--
-- TOC entry 3558 (class 0 OID 0)
-- Dependencies: 250
-- Name: FUNCTION fn_on_payment_verified(); Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON FUNCTION public.fn_on_payment_verified() IS 'Otomatis mengubah status booking menjadi active_stay dan status kamar menjadi terisi saat pembayaran diverifikasi admin';


--
-- TOC entry 249 (class 1255 OID 68011)
-- Name: fn_set_updated_at(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_set_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_set_updated_at() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 67895)
-- Name: bookings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bookings (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    kode_booking character varying(20) NOT NULL,
    user_id uuid NOT NULL,
    room_id uuid NOT NULL,
    tipe_sewa public.stay_type NOT NULL,
    tanggal_check_in date NOT NULL,
    tanggal_check_out date NOT NULL,
    durasi integer NOT NULL,
    harga_snapshot numeric(12,2) NOT NULL,
    total_tagihan numeric(12,2) NOT NULL,
    catatan_penyewa text,
    status public.booking_status DEFAULT 'pending'::public.booking_status NOT NULL,
    alasan_pembatalan text,
    dikonfirmasi_oleh uuid,
    tanggal_konfirmasi timestamp with time zone,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    bukti_transfer character varying(255),
    CONSTRAINT chk_checkout_setelah_checkin CHECK ((tanggal_check_out > tanggal_check_in)),
    CONSTRAINT chk_durasi_positif CHECK ((durasi > 0)),
    CONSTRAINT chk_tagihan_positif CHECK ((total_tagihan > (0)::numeric))
);


ALTER TABLE public.bookings OWNER TO postgres;

--
-- TOC entry 3559 (class 0 OID 0)
-- Dependencies: 217
-- Name: TABLE bookings; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.bookings IS 'Pemesanan kamar oleh penyewa';


--
-- TOC entry 3560 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN bookings.harga_snapshot; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.bookings.harga_snapshot IS 'Snapshot harga per unit saat booking, tidak berubah meski harga kamar diperbarui';


--
-- TOC entry 224 (class 1259 OID 68056)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 68064)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 68090)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 68089)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 3561 (class 0 OID 0)
-- Dependencies: 229
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 234 (class 1259 OID 68142)
-- Name: guest_profile; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.guest_profile (
    id uuid NOT NULL,
    name character varying(255),
    eyebrow character varying(255),
    description text,
    main_photo character varying(255),
    stories json,
    commitment_label character varying(255),
    commitment_title text,
    commitments json,
    important_label character varying(255),
    important_title text,
    important_description text,
    important_items json,
    gallery_label character varying(255),
    gallery_title text,
    gallery_description text,
    gallery_items json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    contact_label character varying(255),
    contact_title text,
    contact_description text,
    contact_button_label character varying(255),
    contact_items json,
    platform_title text,
    platform_description text,
    platform_links json,
    contact_faq_label character varying(255),
    contact_faq_title text,
    contact_faq_description text,
    contact_faqs json,
    feedback_title text,
    feedback_yes_label character varying(255),
    feedback_no_label character varying(255),
    feedback_prompt text,
    feedback_help_title text,
    feedback_wa_label character varying(255),
    location_label character varying(255),
    location_title text,
    location_description text,
    location_embed_url text,
    location_name text,
    location_address text,
    location_google_maps_url text,
    location_waze_url text,
    location_notes json,
    masukkan json
);


ALTER TABLE public.guest_profile OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 68112)
-- Name: guest_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.guest_requests (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    phone character varying(255) NOT NULL,
    email character varying(255),
    request_type character varying(255) NOT NULL,
    checkin date,
    checkout date,
    tipe_kamar character varying(255),
    tipe_sewa character varying(255),
    jumlah_tamu character varying(255),
    budget numeric(12,2),
    kode_booking character varying(255),
    metode_pembayaran character varying(255),
    nama_pengirim character varying(255),
    complaint_category character varying(255),
    pesan text,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.guest_requests OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 68111)
-- Name: guest_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.guest_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.guest_requests_id_seq OWNER TO postgres;

--
-- TOC entry 3562 (class 0 OID 0)
-- Dependencies: 231
-- Name: guest_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.guest_requests_id_seq OWNED BY public.guest_requests.id;


--
-- TOC entry 228 (class 1259 OID 68082)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 68073)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 68072)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 3563 (class 0 OID 0)
-- Dependencies: 226
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 223 (class 1259 OID 68049)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 68048)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 3564 (class 0 OID 0)
-- Dependencies: 222
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 218 (class 1259 OID 67962)
-- Name: notifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notifications (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    judul character varying(150) NOT NULL,
    pesan text NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.notifications OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 68335)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 68020)
-- Name: payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payments (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    kode_pembayaran character varying(25) NOT NULL,
    booking_id uuid NOT NULL,
    jumlah numeric(12,2) NOT NULL,
    metode_pembayaran public.payment_method DEFAULT 'transfer_bank'::public.payment_method NOT NULL,
    nama_bank character varying(50),
    nama_pengirim character varying(100),
    tanggal_transfer date,
    bukti_pembayaran character varying(255),
    status public.payment_status DEFAULT 'menunggu_verifikasi'::public.payment_status NOT NULL,
    diverifikasi_oleh uuid,
    tanggal_verifikasi timestamp with time zone,
    alasan_penolakan text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT chk_jumlah_positif CHECK ((jumlah > (0)::numeric))
);


ALTER TABLE public.payments OWNER TO postgres;

--
-- TOC entry 3565 (class 0 OID 0)
-- Dependencies: 221
-- Name: TABLE payments; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.payments IS 'Log verifikasi pembayaran manual. Admin memverifikasi via WA, lalu input data ke sistem.';


--
-- TOC entry 3566 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN payments.diverifikasi_oleh; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.payments.diverifikasi_oleh IS 'ID Admin yang melakukan verifikasi bukti pembayaran di dashboard.';


--
-- TOC entry 233 (class 1259 OID 68122)
-- Name: room_reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.room_reviews (
    id uuid NOT NULL,
    room_id uuid NOT NULL,
    user_id uuid NOT NULL,
    rating smallint NOT NULL,
    comment text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.room_reviews OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 67879)
-- Name: rooms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rooms (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    nomor_kamar character varying(10) NOT NULL,
    lantai smallint DEFAULT 1 NOT NULL,
    luas_m2 numeric(5,2),
    deskripsi text,
    fasilitas text[],
    harga_harian numeric(12,2) NOT NULL,
    harga_bulanan numeric(12,2) NOT NULL,
    deposit numeric(12,2) DEFAULT 0 NOT NULL,
    status public.room_status DEFAULT 'tersedia'::public.room_status NOT NULL,
    foto_utama character varying(255),
    is_published boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    tipe_kamar character varying(255),
    gallery_images json,
    video_path character varying(255)
);


ALTER TABLE public.rooms OWNER TO postgres;

--
-- TOC entry 3567 (class 0 OID 0)
-- Dependencies: 216
-- Name: TABLE rooms; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.rooms IS 'Data kamar. Semua tipe standard dengan kamar mandi dalam';


--
-- TOC entry 3568 (class 0 OID 0)
-- Dependencies: 216
-- Name: COLUMN rooms.fasilitas; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.rooms.fasilitas IS 'Daftar fasilitas kamar dalam bentuk array teks';


--
-- TOC entry 3569 (class 0 OID 0)
-- Dependencies: 216
-- Name: COLUMN rooms.deposit; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.rooms.deposit IS 'Uang jaminan yang dibayar di awal sewa';


--
-- TOC entry 236 (class 1259 OID 68342)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id uuid,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 67978)
-- Name: system_settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_settings (
    key character varying(100) NOT NULL,
    value text NOT NULL,
    deskripsi text,
    updated_at timestamp with time zone DEFAULT now() NOT NULL,
    hero_label text,
    hero_title text,
    hero_description text,
    how_label text,
    how_title text,
    how_description text,
    how_step_1_title text,
    how_step_1_description text,
    how_step_2_title text,
    how_step_2_description text,
    how_step_3_title text,
    how_step_3_description text,
    rooms_label text,
    rooms_title text,
    rooms_description text,
    gallery_label text,
    gallery_title text,
    gallery_description text,
    facilities_label text,
    facilities_title text,
    facilities_description text,
    hero_image text
);


ALTER TABLE public.system_settings OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 67863)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    nama_lengkap character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    password_hash character varying(255) NOT NULL,
    no_telepon character varying(20) NOT NULL,
    no_ktp character varying(20),
    role public.user_role DEFAULT 'penyewa'::public.user_role NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    remember_token character varying(100),
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 3570 (class 0 OID 0)
-- Dependencies: 215
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.users IS 'Pengguna sistem: admin dan penyewa';


--
-- TOC entry 3571 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN users.no_ktp; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.users.no_ktp IS 'Nomor KTP untuk identifikasi penyewa';


--
-- TOC entry 220 (class 1259 OID 67991)
-- Name: v_room_availability; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_room_availability AS
 SELECT r.id,
    r.nomor_kamar,
    r.lantai,
    r.harga_harian,
    r.harga_bulanan,
    r.status AS status_kamar,
    b.id AS booking_aktif_id,
    b.tipe_sewa,
    b.tanggal_check_in,
    b.tanggal_check_out,
    u.nama_lengkap AS nama_penyewa_aktif,
    u.no_telepon AS telepon_penyewa_aktif
   FROM ((public.rooms r
     LEFT JOIN public.bookings b ON (((b.room_id = r.id) AND (b.status = ANY (ARRAY['active_stay'::public.booking_status, 'menunggu_konfirmasi'::public.booking_status])) AND ((CURRENT_DATE >= b.tanggal_check_in) AND (CURRENT_DATE <= b.tanggal_check_out)))))
     LEFT JOIN public.users u ON ((u.id = b.user_id)))
  WHERE (r.is_published = true);


ALTER TABLE public.v_room_availability OWNER TO postgres;

--
-- TOC entry 3572 (class 0 OID 0)
-- Dependencies: 220
-- Name: VIEW v_room_availability; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON VIEW public.v_room_availability IS 'Status ketersediaan seluruh kamar pada hari ini';


--
-- TOC entry 3305 (class 2604 OID 68093)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 3307 (class 2604 OID 68115)
-- Name: guest_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.guest_requests ALTER COLUMN id SET DEFAULT nextval('public.guest_requests_id_seq'::regclass);


--
-- TOC entry 3304 (class 2604 OID 68076)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 3303 (class 2604 OID 68052)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 3531 (class 0 OID 67895)
-- Dependencies: 217
-- Data for Name: bookings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bookings (id, kode_booking, user_id, room_id, tipe_sewa, tanggal_check_in, tanggal_check_out, durasi, harga_snapshot, total_tagihan, catatan_penyewa, status, alasan_pembatalan, dikonfirmasi_oleh, tanggal_konfirmasi, created_at, updated_at, bukti_transfer) FROM stdin;
019e7c28-4d90-7218-a874-12aac455d483	BK26053190X0OG	019e7c24-3eb8-70bf-96a5-299ca338e4c6	4b7fe612-de3f-4325-aeef-ec2cbfa087d7	harian	2026-06-01	2026-06-04	3	150000.00	950000.00	Alamat penyewa: Jl. Kiageng Kuning, Kuningam, Kec. Kanigoro, Kab. Blitar, Jawa timur, indonesia\n\nhaiiiii	active_stay	\N	\N	2026-05-31 03:51:27+07	2026-05-31 03:51:16+07	2026-05-31 10:51:27.202627+07	booking-payment-proofs/SBW2Wd2QsQA5pCtAdVC9df3G8w3tqxuXuGUvrUX0.png
019e8603-e62c-71e5-9ad2-ec18fb595d8a	BK260602U4O01N	019e7c24-3eb8-70bf-96a5-299ca338e4c6	20acaea2-50c9-40a4-a3fe-be96316fcbd5	harian	2026-06-03	2026-06-05	2	150000.00	800000.00	Iya	active_stay	\N	019e7c24-3eb8-70bf-96a5-299ca338e4c6	2026-06-02 01:48:13+07	2026-06-02 01:47:43+07	2026-06-02 08:48:13.257979+07	booking-payment-proofs/1eGgfVz4EAexFqwQ7T6nyeL7zCC229FrpflNJKsr.png
019e7c03-d09e-724e-9fbd-f2474f91719c	BK2605313EJCE6	019e7c03-d096-73e0-bc65-2fab6cff79b9	20acaea2-50c9-40a4-a3fe-be96316fcbd5	harian	2026-05-31	2026-06-01	1	150000.00	650000.00	Alamat penyewa: Jl. Kiageng Kuning, Kuningam, Kec. Kanigoro, Kab. Blitar, Jawa timur, indonesia\n\nyayayayayayayayayayay	selesai	\N	\N	\N	2026-05-31 03:11:25+07	2026-06-08 23:57:52.455201+07	\N
019eac23-8a3b-7054-9a00-56952c737c1d	BK2606090BLGB9	b45a9901-5fe5-4ba0-b301-7d4650c068ed	019ea9ef-31c2-7178-9787-c4decc52bd8e	harian	2026-06-09	2026-06-10	1	15000.00	715000.00	jjjjjjjjjjjjjjjj uuuuuuuuuuuuu	active_stay	\N	b45a9901-5fe5-4ba0-b301-7d4650c068ed	2026-06-09 11:29:08+07	2026-06-09 11:27:50+07	2026-06-09 18:29:08.22077+07	booking-payment-proofs/qKNVMGZZRtYDijXbrmkHf2NnnrZwdZx6t3tzObKh.jpg
\.


--
-- TOC entry 3537 (class 0 OID 68056)
-- Dependencies: 224
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
laravel-cache-1bcd4a4ff2932130b5173cf2a5edf58e08269d18:timer	i:1781029086;	1781029086
laravel-cache-1bcd4a4ff2932130b5173cf2a5edf58e08269d18	i:3;	1781029086
\.


--
-- TOC entry 3538 (class 0 OID 68064)
-- Dependencies: 225
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 3543 (class 0 OID 68090)
-- Dependencies: 230
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 3547 (class 0 OID 68142)
-- Dependencies: 234
-- Data for Name: guest_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.guest_profile (id, name, eyebrow, description, main_photo, stories, commitment_label, commitment_title, commitments, important_label, important_title, important_description, important_items, gallery_label, gallery_title, gallery_description, gallery_items, created_at, updated_at, contact_label, contact_title, contact_description, contact_button_label, contact_items, platform_title, platform_description, platform_links, contact_faq_label, contact_faq_title, contact_faq_description, contact_faqs, feedback_title, feedback_yes_label, feedback_no_label, feedback_prompt, feedback_help_title, feedback_wa_label, location_label, location_title, location_description, location_embed_url, location_name, location_address, location_google_maps_url, location_waze_url, location_notes, masukkan) FROM stdin;
18cde2ea-b69a-4447-b375-78572cbe4b5b	Berlima Guest House 	Tentang Kami	Berlima Guest House adalah tempat tinggal sementara yang dirancang untuk tamu yang membutuhkan kamar nyaman, proses reservasi jelas, dan komunikasi yang mudah dengan admin. Kami melayani penyewa harian maupun bulanan dengan fokus pada ketenangan, kebersihan, dan kepastian informasi sebelum tamu datang.	guest-profile/01KTP2ERX6V0DWSQX3WP6BGX8F.png	[{"title":"Pengalaman Menginap yang Praktis","description":"Setiap informasi kamar, harga, deposit, dan status ketersediaan ditampilkan agar calon penyewa dapat mengambil keputusan dengan lebih percaya diri sebelum mengirim reservasi."},{"title":"Komunikasi Tetap Personal","description":"Sistem membantu menghitung dan mencatat permintaan, sementara admin tetap melakukan konfirmasi akhir melalui WhatsApp supaya tidak ada informasi yang terlewat."}]	Komitmen Kami	Hal yang kami jaga untuk setiap tamu	[{"title":"Kamar yang layak dan siap digunakan","description":"Kebersihan, fasilitas dasar, dan kesiapan kamar menjadi bagian utama sebelum tamu masuk."},{"title":"Informasi harga yang jelas","description":"Total tagihan mengikuti data kamar dan pilihan reservasi yang dihitung oleh sistem."},{"title":"Konfirmasi sebelum pembayaran","description":"Pembayaran dilakukan setelah admin memastikan kamar dan mengirim instruksi resmi."}]	Informasi Penting	Sebelum mengirim reservasi	Berlima Guest House memproses reservasi melalui pengecekan admin agar tanggal, kamar, dan pembayaran tetap jelas untuk calon penyewa.	[{"title":"Reservasi belum otomatis diterima","description":"Data yang dikirim masuk sebagai permintaan reservasi dan akan dicek terlebih dahulu."},{"title":"Admin menghubungi melalui WhatsApp","description":"Admin akan mengonfirmasi ketersediaan kamar melalui nomor WhatsApp yang Anda isi."},{"title":"Pembayaran setelah konfirmasi","description":"Jangan melakukan pembayaran sebelum admin mengirim instruksi dan memastikan kamar."},{"title":"Total dihitung sistem","description":"Total tagihan mengikuti tipe sewa, tanggal, durasi, dan harga kamar yang dipilih."}]	Galeri	Sekilas Suasana Berlima	Lihat suasana kamar, area bersama, dan tampak guest house sebelum Anda datang.	[{"title":"Kamar Nyaman","image":"guest-gallery\\/01KTP2ERXF8HY9S7WEC1NDTYXG.png"},{"title":"Tampak Depan","image":"guest-gallery\\/01KTP2ERXP7JB7XKDNSZ8CGQ1R.png"},{"title":"Ruang Santai","image":"guest-gallery\\/01KTP2ERXVF0NP2BRZP2MEADNG.png"},{"title":"Area Umum","image":"guest-gallery\\/01KTP2ERY173E0NRARKZNZAB84.png"}]	2026-05-31 09:30:38	2026-06-10 02:39:55	Kontak	Bagaimana kami dapat membantu Anda?	Ajukan pertanyaan seputar kamar, ketersediaan, pembayaran, perubahan jadwal, atau kebutuhan lain yang ingin Anda pastikan sebelum menginap.	Ajukan Pertanyaan	[{"label":"WhatsApp Admin","value":"0857-0732-3326","url":"https:\\/\\/wa.me\\/6285707323326"},{"label":"Surel","value":"info@berlimaguesthouse.com","url":"mailto:info@berlimaguesthouse.com"},{"label":"Jam Operasional","value":"Setiap hari, 07.00 - 22.00 WIB","url":null},{"label":"Masuk \\/ Keluar","value":"Masuk 14.00 WIB, keluar 12.00 WIB","url":null}]	Platform Digital	Ikuti kanal resmi kami untuk informasi kamar, lokasi, dan pembaruan layanan.	[{"label":"Instagram","url":"https:\\/\\/instagram.com\\/berlima_guesthouse"},{"label":"TikTok","url":"https:\\/\\/tiktok.com\\/@berlima"},{"label":"Google Maps","url":"https:\\/\\/maps.google.com"},{"label":"WhatsApp","url":"https:\\/\\/wa.me\\/6285707323326"}]	Pertanyaan Umum	Pertanyaan yang sering diajukan	Beberapa jawaban singkat sebelum Anda menghubungi admin atau mengisi formulir.	[{"question":"Apakah reservasi langsung diterima otomatis?","answer":"Tidak. Reservasi yang Anda kirim akan masuk sebagai permintaan dan admin akan mengecek ketersediaan kamar terlebih dahulu."},{"question":"Bagaimana saya tahu kamar masih tersedia?","answer":"Admin akan menghubungi Anda melalui WhatsApp untuk mengonfirmasi ketersediaan kamar pada tanggal yang dipilih."},{"question":"Kapan saya harus melakukan pembayaran?","answer":"Pembayaran dilakukan setelah admin mengonfirmasi kamar dan memberikan informasi pembayaran resmi."},{"question":"Apakah total tagihan dihitung manual?","answer":"Tidak. Total tagihan dihitung otomatis oleh sistem berdasarkan kamar, tipe sewa, tanggal masuk, tanggal keluar, dan durasi."},{"question":"Apa yang terjadi setelah saya mengirim reservasi?","answer":"Admin akan menghubungi nomor WhatsApp yang Anda cantumkan untuk konfirmasi ketersediaan kamar dan tahap pembayaran."},{"question":"Apakah bisa memesan tanpa masuk akun?","answer":"Bisa. Jika belum masuk akun, Anda akan diminta mengisi data diri seperti nama, nomor WhatsApp, surel, dan alamat opsional."},{"question":"Bagaimana jika tanggal yang saya pilih bentrok?","answer":"Admin akan memberi tahu jika kamar sudah dipesan atau sedang tidak tersedia, lalu membantu menawarkan tanggal atau kamar lain bila memungkinkan."},{"question":"Bisakah saya mengubah tanggal masuk atau keluar?","answer":"Perubahan tanggal dapat dibicarakan dengan admin melalui WhatsApp selama kamar masih tersedia dan reservasi belum diproses final."}]	Apakah ini membantu?	Ya	Tidak	Mohon maaf, apakah Anda memiliki masukan?	Perlu bantuan lain?	Hubungi Kami	Lokasi	Temukan Kami	Berlima Guest House berada di lokasi yang mudah dijangkau. Gunakan peta untuk melihat area sekitar atau membuka rute melalui aplikasi navigasi.	https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.50938823!2d112.60363795!3d-7.2574719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fbf8381ac47f%3A0x3027a76e352be40!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1698000000000!5m2!1sen!2sid	Berlima Guest House	Jl. Contoh No. 5, Surabaya	https://maps.google.com	https://waze.com	["Parkir tersedia untuk tamu.","Masuk lebih awal dapat dikonfirmasi lebih dulu kepada admin.","Mohon simpan nomor WhatsApp admin untuk koordinasi kedatangan."]	[{"message":"Masukan oiiiiiiii","created_at":"2026-06-09 11:55:14"}]
\.


--
-- TOC entry 3545 (class 0 OID 68112)
-- Dependencies: 232
-- Data for Name: guest_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.guest_requests (id, nama, phone, email, request_type, checkin, checkout, tipe_kamar, tipe_sewa, jumlah_tamu, budget, kode_booking, metode_pembayaran, nama_pengirim, complaint_category, pesan, status, created_at, updated_at) FROM stdin;
1	Gigan Tizm	085707323326	gigantizm246@gmail.com	reschedule	2026-05-05	\N	\N	\N	\N	\N	\N	\N	\N	\N	Tes	pending	2026-05-11 08:48:22	2026-05-11 08:48:22
3	MOCH ADAM ARSYAD FAIZIN	+6285707323326	adamkian09@gmail.com	complaint	\N	\N	\N	\N	\N	\N	\N	\N	\N	Kamar	kurang banget	pending	2026-06-09 11:56:51	2026-06-09 11:56:51
\.


--
-- TOC entry 3541 (class 0 OID 68082)
-- Dependencies: 228
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- TOC entry 3540 (class 0 OID 68073)
-- Dependencies: 227
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 3536 (class 0 OID 68049)
-- Dependencies: 223
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000001_create_cache_table	1
2	0001_01_01_000002_create_jobs_table	1
4	2026_05_11_075633_create_guest_requests_table	2
5	2026_05_11_083950_add_tipe_kamar_to_rooms_table	3
6	2026_05_31_000001_create_room_reviews_table	4
7	2026_05_31_000002_add_bukti_transfer_to_bookings_table	5
8	2026_05_31_000005_add_contact_fields_to_guest_profile_table	6
9	2026_06_01_000001_add_media_fields_to_rooms_table	7
10	0001_01_01_000000_create_users_table	8
11	2026_04_29_000001_create_rooms_table	8
12	2026_04_29_000002_add_role_to_users_table	8
13	2026_05_11_000003_add_phone_ktp_to_users_table	8
14	2026_05_11_090000_create_bookings_table	8
15	2026_05_31_000003_create_system_settings_table	8
16	2026_05_31_000004_create_guest_profile_table	8
17	2026_06_10_000001_add_hero_image_to_system_settings_table	9
\.


--
-- TOC entry 3532 (class 0 OID 67962)
-- Dependencies: 218
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifications (id, user_id, judul, pesan, is_read, created_at) FROM stdin;
\.


--
-- TOC entry 3548 (class 0 OID 68335)
-- Dependencies: 235
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 3534 (class 0 OID 68020)
-- Dependencies: 221
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payments (id, kode_pembayaran, booking_id, jumlah, metode_pembayaran, nama_bank, nama_pengirim, tanggal_transfer, bukti_pembayaran, status, diverifikasi_oleh, tanggal_verifikasi, alasan_penolakan, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 3546 (class 0 OID 68122)
-- Dependencies: 233
-- Data for Name: room_reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.room_reviews (id, room_id, user_id, rating, comment, created_at, updated_at) FROM stdin;
019e7c24-b89f-70e2-94d8-e7f74229f182	20acaea2-50c9-40a4-a3fe-be96316fcbd5	019e7c24-3eb8-70bf-96a5-299ca338e4c6	5	mantab	2026-05-31 03:47:21	2026-05-31 03:47:21
\.


--
-- TOC entry 3530 (class 0 OID 67879)
-- Dependencies: 216
-- Data for Name: rooms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rooms (id, nomor_kamar, lantai, luas_m2, deskripsi, fasilitas, harga_harian, harga_bulanan, deposit, status, foto_utama, is_published, created_at, updated_at, tipe_kamar, gallery_images, video_path) FROM stdin;
20acaea2-50c9-40a4-a3fe-be96316fcbd5	101	1	16.00	Kamar lantai 1, dekat parkiran	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Parkir motor"}	150000.00	1500000.00	500000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.507136+07	\N	\N	\N
4b7fe612-de3f-4325-aeef-ec2cbfa087d7	102	1	16.00	Kamar lantai 1, jendela menghadap taman	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Parkir motor"}	150000.00	1500000.00	500000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.517692+07	\N	\N	\N
afa84b21-dcf6-4938-9605-456422cd0744	201	2	16.00	Kamar lantai 2, pemandangan jalan utama	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Parkir motor"}	160000.00	1600000.00	500000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.520635+07	\N	\N	\N
dd80e2b2-3a8d-43f5-b441-40c4d0826987	202	2	16.00	Kamar lantai 2, sudut, lebih privat	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Parkir motor"}	160000.00	1600000.00	500000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.521649+07	\N	\N	\N
e56d8db3-8292-4baf-b34a-3ce492ba2746	203	2	18.00	Kamar lantai 2, ada balkon kecil	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Televisi LED","Parkir motor"}	185000.00	1850000.00	500000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.522482+07	\N	\N	\N
c56c7e74-9197-4652-9cd0-b6d23456702e	301	3	20.00	Kamar lantai 3, terluas, pemandangan terbaik	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Televisi LED","Kulkas mini","Parkir motor"}	200000.00	2000000.00	750000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.523237+07	\N	\N	\N
37808a3f-e993-4515-a673-54f30d91037e	302	3	20.00	Kamar lantai 3, tenang dan sejuk	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Televisi LED","Kulkas mini","Parkir motor"}	200000.00	2000000.00	750000.00	tersedia	\N	t	2026-04-30 21:40:06.286894+07	2026-06-08 23:12:56.524051+07	\N	\N	\N
019ea9ef-31c2-7178-9787-c4decc52bd8e	A-303	2	18.00	kamar tes	{AC}	15000.00	300000.00	700000.00	terisi	rooms/01KTMYYCCH4PA0MSR89HTBAXFT.png	t	2026-06-09 01:11:25+07	2026-06-09 18:29:08.226963+07	\N	["rooms\\/gallery\\/01KTMYYCD5RM0QTPYNF9MEJP2M.jpg"]	\N
019eac2c-9697-711f-b817-01a6eaabd167	A-404	2	10.00	Kamar keren	{AC,Kulkas,"Pemanas Air"}	150000.00	3000000.00	600000.00	tersedia	rooms/01KTP2S5K951ZP8VFW8HJ8DB3X.png	t	2026-06-09 11:37:43+07	2026-06-09 18:53:58.944444+07	\N	["rooms\\/gallery\\/01KTP3PXWH1KM4RYDMGXKZ5G75.png"]	rooms/videos/01KTP2S5KK0017TVH6NTPRSRCP.mp4
cfeb9b7f-71f9-47f6-9d34-8b4f4c45e4ea	103	1	18.00	Kamar lantai 1, lebih luas	{"Pendingin Ruangan",Wi-Fi,Lemari,"Meja belajar","Kasur pegas","Pemanas air","Parkir motor"}	175000.00	1750000.00	500000.00	tersedia	rooms/01KTPSMZKEA1D0FM6004EXPD0Z.png	t	2026-04-30 21:40:06.286894+07	2026-06-10 01:17:23.872813+07	\N	["rooms\\/gallery\\/01KTPSMZM8XFWEBNRT8KP56Y68.png"]	rooms/videos/01KTPSMZMCRN2YVQV1Q8VW1GG5.mp4
\.


--
-- TOC entry 3549 (class 0 OID 68342)
-- Dependencies: 236
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- TOC entry 3533 (class 0 OID 67978)
-- Dependencies: 219
-- Data for Name: system_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_settings (key, value, deskripsi, updated_at, hero_label, hero_title, hero_description, how_label, how_title, how_description, how_step_1_title, how_step_1_description, how_step_2_title, how_step_2_description, how_step_3_title, how_step_3_description, rooms_label, rooms_title, rooms_description, gallery_label, gallery_title, gallery_description, facilities_label, facilities_title, facilities_description, hero_image) FROM stdin;
wa_admin_number	6281234567890	Nomor WhatsApp admin untuk konfirmasi pembayaran	2026-04-30 21:40:06.231968+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
alamat_bisnis	Jl. Soekarno Hatta No. 9, Malang	Alamat bisnis	2026-04-30 21:40:06.231968+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
batas_waktu_pembayaran	24	Batas waktu unggah bukti pembayaran (jam)	2026-06-08 16:12:56+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
reminder_checkout_jam	24	Jam sebelum keluar untuk mengirim pengingat	2026-06-08 16:12:56+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
nama_bisnis	Kost dan Wisma	Nama bisnis	2026-06-08 16:12:56+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
home_landing	home_landing	Pengaturan konten landing page.	2026-06-10 04:00:15+07	Selamat Datang di Berlima	Temukan\nBerlima Guest\nHouse	Nikmati hunian nyaman dengan fasilitas lengkap dan lokasi strategis.\nTemukan tempat tinggal yang sesuai dengan kebutuhan Anda.	Cara Pemesanan	Pemesanan Kamar\nSemudah 3 Langkah	Proses reservasi kami dirancang sederhana dan cepat, mulai dari memilih kamar hingga masuk kamar.	Pilih kamar	Buka daftar kamar, periksa foto, harga, fasilitas, penilaian, dan pilih kamar yang sesuai kebutuhan.	Isi data reservasi	Lengkapi data diri, tipe sewa, tanggal masuk, tanggal keluar, dan catatan reservasi.	Tunggu konfirmasi admin melalui WhatsApp	Admin akan mengecek ketersediaan kamar lalu menghubungi Anda melalui WhatsApp untuk konfirmasi berikutnya.	Kamar Pilihan	Hunian Nyaman\nuntuk Semua Kebutuhan	Pilih kamar sesuai durasi dan kebutuhan kamu — harian, mingguan, atau bulanan.\nSemua dilengkapi fasilitas modern di lingkungan yang aman dan tenang.	Sekilas Suasana	Lihat Kenyamanan\ndari Dekat	Intip suasana kamar dan fasilitas kami sebelum Anda datang.\nNyaman, bersih, dan siap menemani waktu istirahat Anda.	Hubungi Kami	Butuh Bantuan\natau Konfirmasi Kamar?	Tim Berlima Guest House siap membantu pertanyaan kamar, ketersediaan tanggal, pembayaran, dan kebutuhan lain melalui halaman kontak.	/images/gallery/exterior.png
\.


--
-- TOC entry 3529 (class 0 OID 67863)
-- Dependencies: 215
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, nama_lengkap, email, password_hash, no_telepon, no_ktp, role, is_active, remember_token, created_at, updated_at) FROM stdin;
019e7c03-d096-73e0-bc65-2fab6cff79b9	teeeeee	adam36@gmail.com	$2y$12$2lj/y2agC3ZU3.cjVhy4zeXTvkBF0IPquLhpWX/o8xXXYt1Jwc/Lm	085707323326	\N	penyewa	t	\N	2026-05-31 03:11:25+07	2026-05-31 03:11:25+07
019e7c24-3eb8-70bf-96a5-299ca338e4c6	MOCH ADAM ARSYAD FAIZIN	adamkian09@gmail.com	$2y$12$UQ84c6fcj.M2Xn7GRuBc..F2dPyDMXsRJF.n4XpsM17rxYAMCcLTq	085707323327	54545454545454545	penyewa	t	\N	2026-05-31 03:46:50+07	2026-05-31 03:46:50+07
b45a9901-5fe5-4ba0-b301-7d4650c068ed	Admin Berlima	admin@berlima.test	$2y$12$2YVFYjCksrQaTKEw/E/wOOpoSUOOPtmA1fc0bpM7Ms3gajGcFXQAK	081234567890	\N	admin	t	It8Yw0ZDz1Pm7YoHcLHMzbbEp4nOkpNildtmaylvdQvmR70IsOyZl25Qtsnb	2026-04-30 21:40:06.280811+07	2026-06-09 19:14:56.24883+07
\.


--
-- TOC entry 3573 (class 0 OID 0)
-- Dependencies: 229
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 3574 (class 0 OID 0)
-- Dependencies: 231
-- Name: guest_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.guest_requests_id_seq', 3, true);


--
-- TOC entry 3575 (class 0 OID 0)
-- Dependencies: 226
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 3576 (class 0 OID 0)
-- Dependencies: 222
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 17, true);


--
-- TOC entry 3324 (class 2606 OID 67910)
-- Name: bookings bookings_kode_booking_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_kode_booking_key UNIQUE (kode_booking);


--
-- TOC entry 3326 (class 2606 OID 67908)
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- TOC entry 3350 (class 2606 OID 68070)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 3347 (class 2606 OID 68062)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 3357 (class 2606 OID 68098)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 3359 (class 2606 OID 68100)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 3367 (class 2606 OID 68148)
-- Name: guest_profile guest_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.guest_profile
    ADD CONSTRAINT guest_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3361 (class 2606 OID 68120)
-- Name: guest_requests guest_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.guest_requests
    ADD CONSTRAINT guest_requests_pkey PRIMARY KEY (id);


--
-- TOC entry 3355 (class 2606 OID 68088)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 3352 (class 2606 OID 68080)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 3344 (class 2606 OID 68054)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 3334 (class 2606 OID 67971)
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- TOC entry 3369 (class 2606 OID 68341)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 3340 (class 2606 OID 68034)
-- Name: payments payments_kode_pembayaran_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_kode_pembayaran_key UNIQUE (kode_pembayaran);


--
-- TOC entry 3342 (class 2606 OID 68032)
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- TOC entry 3363 (class 2606 OID 68140)
-- Name: room_reviews room_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_reviews
    ADD CONSTRAINT room_reviews_pkey PRIMARY KEY (id);


--
-- TOC entry 3320 (class 2606 OID 67894)
-- Name: rooms rooms_nomor_kamar_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_nomor_kamar_key UNIQUE (nomor_kamar);


--
-- TOC entry 3322 (class 2606 OID 67892)
-- Name: rooms rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_pkey PRIMARY KEY (id);


--
-- TOC entry 3372 (class 2606 OID 68348)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 3336 (class 2606 OID 67985)
-- Name: system_settings system_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_settings
    ADD CONSTRAINT system_settings_pkey PRIMARY KEY (key);


--
-- TOC entry 3314 (class 2606 OID 67876)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3316 (class 2606 OID 67878)
-- Name: users users_no_telepon_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_no_telepon_key UNIQUE (no_telepon);


--
-- TOC entry 3318 (class 2606 OID 67874)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3345 (class 1259 OID 68063)
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- TOC entry 3348 (class 1259 OID 68071)
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- TOC entry 3327 (class 1259 OID 67930)
-- Name: idx_bookings_room_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bookings_room_aktif ON public.bookings USING btree (room_id, tanggal_check_in, tanggal_check_out) WHERE (status = ANY (ARRAY['pending'::public.booking_status, 'menunggu_konfirmasi'::public.booking_status, 'active_stay'::public.booking_status]));


--
-- TOC entry 3328 (class 1259 OID 67927)
-- Name: idx_bookings_room_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bookings_room_id ON public.bookings USING btree (room_id);


--
-- TOC entry 3329 (class 1259 OID 67928)
-- Name: idx_bookings_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bookings_status ON public.bookings USING btree (status);


--
-- TOC entry 3330 (class 1259 OID 67929)
-- Name: idx_bookings_tanggal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bookings_tanggal ON public.bookings USING btree (tanggal_check_in, tanggal_check_out);


--
-- TOC entry 3331 (class 1259 OID 67926)
-- Name: idx_bookings_user_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bookings_user_id ON public.bookings USING btree (user_id);


--
-- TOC entry 3332 (class 1259 OID 67977)
-- Name: idx_notif_user_unread; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_notif_user_unread ON public.notifications USING btree (user_id, is_read, created_at DESC);


--
-- TOC entry 3337 (class 1259 OID 68045)
-- Name: idx_payments_booking_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_payments_booking_id ON public.payments USING btree (booking_id);


--
-- TOC entry 3338 (class 1259 OID 68046)
-- Name: idx_payments_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_payments_status ON public.payments USING btree (status);


--
-- TOC entry 3353 (class 1259 OID 68081)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 3364 (class 1259 OID 68137)
-- Name: room_reviews_room_id_created_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX room_reviews_room_id_created_at_index ON public.room_reviews USING btree (room_id, created_at);


--
-- TOC entry 3365 (class 1259 OID 68138)
-- Name: room_reviews_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX room_reviews_user_id_index ON public.room_reviews USING btree (user_id);


--
-- TOC entry 3370 (class 1259 OID 68350)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 3373 (class 1259 OID 68349)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- TOC entry 3384 (class 2620 OID 68014)
-- Name: bookings trg_bookings_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_bookings_updated_at BEFORE UPDATE ON public.bookings FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- TOC entry 3385 (class 2620 OID 68019)
-- Name: bookings trg_no_double_booking; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_no_double_booking BEFORE INSERT OR UPDATE ON public.bookings FOR EACH ROW WHEN ((new.status <> ALL (ARRAY['dibatalkan'::public.booking_status, 'selesai'::public.booking_status]))) EXECUTE FUNCTION public.fn_check_double_booking();


--
-- TOC entry 3383 (class 2620 OID 68013)
-- Name: rooms trg_rooms_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_rooms_updated_at BEFORE UPDATE ON public.rooms FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- TOC entry 3382 (class 2620 OID 68012)
-- Name: users trg_users_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- TOC entry 3374 (class 2606 OID 67921)
-- Name: bookings bookings_dikonfirmasi_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_dikonfirmasi_oleh_fkey FOREIGN KEY (dikonfirmasi_oleh) REFERENCES public.users(id);


--
-- TOC entry 3375 (class 2606 OID 67916)
-- Name: bookings bookings_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_room_id_fkey FOREIGN KEY (room_id) REFERENCES public.rooms(id);


--
-- TOC entry 3376 (class 2606 OID 67911)
-- Name: bookings bookings_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3377 (class 2606 OID 67972)
-- Name: notifications notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3378 (class 2606 OID 68035)
-- Name: payments payments_booking_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_booking_id_fkey FOREIGN KEY (booking_id) REFERENCES public.bookings(id) ON DELETE CASCADE;


--
-- TOC entry 3379 (class 2606 OID 68040)
-- Name: payments payments_diverifikasi_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_diverifikasi_oleh_fkey FOREIGN KEY (diverifikasi_oleh) REFERENCES public.users(id);


--
-- TOC entry 3380 (class 2606 OID 68127)
-- Name: room_reviews room_reviews_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_reviews
    ADD CONSTRAINT room_reviews_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id) ON DELETE CASCADE;


--
-- TOC entry 3381 (class 2606 OID 68132)
-- Name: room_reviews room_reviews_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_reviews
    ADD CONSTRAINT room_reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


-- Completed on 2026-06-10 11:17:17

--
-- PostgreSQL database dump complete
--

\unrestrict 0rWq7EmSscWRBM7iJstLjZE72msuoUNeWrLw72XGbFPh2O66wh7hwHvQjAH2iLU

