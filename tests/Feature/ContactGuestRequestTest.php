<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\GuestRequestResource\Pages\EditGuestRequest;
use App\Models\GuestRequest;
use App\Models\Room;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\PanelRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ContactGuestRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_uses_room_choices_for_booking_requests(): void
    {
        $room = Room::create([
            'nomor_kamar' => 'A-404',
            'lantai' => 4,
            'deskripsi' => 'Kamar test contact',
            'harga_harian' => 250000,
            'harga_bulanan' => 2500000,
            'deposit' => 500000,
            'status' => 'tersedia',
            'is_published' => true,
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Pilih Kamar')
            ->assertSee('Kamar A-404 - Lantai 4 - Tersedia - Rp 250.000/malam');

        $response = $this->post(route('contact.store'), [
            'nama' => 'Tamu Contact',
            'phone' => '081234567000',
            'email' => 'tamu.contact@example.test',
            'request_type' => 'booking',
            'checkin' => now()->addDay()->toDateString(),
            'checkout' => now()->addDays(2)->toDateString(),
            'tipe_kamar' => $room->nomor_kamar,
            'tipe_sewa' => 'harian',
            'jumlah_tamu' => '2 orang',
            'pesan' => 'Saya ingin pesan kamar ini.',
        ]);

        $response->assertRedirect();

        $guestRequest = GuestRequest::query()->firstOrFail();

        $this->assertSame('booking', $guestRequest->request_type);
        $this->assertSame('A-404', $guestRequest->tipe_kamar);
        $this->assertSame('pending', $guestRequest->status);
    }

    public function test_contact_management_reason_is_visible_and_admin_can_update_status(): void
    {
        $response = $this->post(route('contact.store'), [
            'nama' => 'Tamu Reschedule',
            'phone' => '081234567001',
            'email' => 'reschedule@example.test',
            'request_type' => 'reschedule',
            'manage_reason' => 'Ingin geser jadwal karena perjalanan berubah.',
            'pesan' => 'Mohon dibantu admin.',
        ]);

        $response->assertRedirect();

        $guestRequest = GuestRequest::query()->firstOrFail();

        $this->assertStringContainsString('Alasan perubahan/pembatalan', (string) $guestRequest->pesan);
        $this->assertStringContainsString('Ingin geser jadwal', (string) $guestRequest->pesan);

        $admin = User::create([
            'nama_lengkap' => 'Admin Contact',
            'email' => 'admin.contact@example.test',
            'password_hash' => Hash::make('password'),
            'no_telepon' => '089999999000',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Filament::setCurrentPanel(app(PanelRegistry::class)->get('admin'));

        Livewire::actingAs($admin)
            ->test(EditGuestRequest::class, ['record' => $guestRequest->getKey()])
            ->set('data.status', 'diproses')
            ->call('save', false, false)
            ->assertHasNoErrors();

        $this->assertSame('diproses', $guestRequest->refresh()->status);
    }
}
