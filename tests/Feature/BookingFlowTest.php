<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Filament\PanelRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_booking_can_be_tracked_after_admin_confirmation(): void
    {
        Storage::fake('public');

        $room = Room::create([
            'nomor_kamar' => 'T-103',
            'lantai' => 1,
            'deskripsi' => 'Kamar test booking',
            'harga_harian' => 175000,
            'harga_bulanan' => 1750000,
            'deposit' => 500000,
            'status' => 'tersedia',
            'is_published' => true,
        ]);

        $response = $this->post(route('rooms.booking.store', ['room' => $room->nomor_kamar]), [
            'nama' => 'Tamu Booking',
            'phone' => '081234567890',
            'email' => 'tamu.booking@example.test',
            'alamat' => 'Jl. Test Booking',
            'tipe_sewa' => 'harian',
            'tanggal_check_in' => now()->addDay()->toDateString(),
            'tanggal_check_out' => now()->addDays(3)->toDateString(),
            'catatan_penyewa' => 'Datang malam.',
            'bukti_transfer' => UploadedFile::fake()->image('bukti-transfer.jpg'),
        ]);

        $response->assertRedirect(route('rooms.booking.create', ['room' => $room->nomor_kamar]));

        $booking = Booking::query()->where('room_id', $room->id)->firstOrFail();

        $this->assertSame('menunggu_konfirmasi', $booking->status);
        $this->assertSame(2, $booking->durasi);
        $this->assertSame('850000.00', (string) $booking->total_tagihan);
        $this->assertNotNull($booking->bukti_transfer);
        Storage::disk('public')->assertExists($booking->bukti_transfer);

        $this->get(route('rooms.booking.create', ['room' => $room->nomor_kamar]))
            ->assertOk()
            ->assertSee($booking->kode_booking)
            ->assertSee('Menunggu Konfirmasi');

        $admin = User::create([
            'nama_lengkap' => 'Admin Booking',
            'email' => 'admin.booking@example.test',
            'password_hash' => Hash::make('password'),
            'no_telepon' => '089999999999',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\BookingResource\Pages\EditBooking::class, [
                'record' => $booking->getRouteKey(),
            ])
            ->set('data.status', 'active_stay')
            ->call('save', false, false)
            ->assertHasNoErrors();

        $booking->refresh();

        $this->withSession(['tracked_booking_id' => $booking->id])
            ->get(route('rooms.booking.create', ['room' => $room->nomor_kamar]))
            ->assertOk()
            ->assertSee($booking->kode_booking)
            ->assertSee('Berhasil / Sedang Menginap')
            ->assertSee('Dikonfirmasi');
    }

    public function test_only_active_admin_users_can_access_filament_panel(): void
    {
        $panel = app(PanelRegistry::class)->get('admin');

        $admin = User::create([
            'nama_lengkap' => 'Admin Booking',
            'email' => 'admin.panel@example.test',
            'password_hash' => Hash::make('password'),
            'no_telepon' => '087777777777',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $tenant = User::create([
            'nama_lengkap' => 'Penyewa Booking',
            'email' => 'penyewa.panel@example.test',
            'password_hash' => Hash::make('password'),
            'no_telepon' => '086666666666',
            'role' => 'penyewa',
            'is_active' => true,
        ]);

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse($tenant->canAccessPanel($panel));

        $this->actingAs($tenant)
            ->get('/admin/bookings')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/bookings')
            ->assertOk();
    }
}
