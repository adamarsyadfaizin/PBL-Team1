<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\RoomResource\Pages\EditRoom;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RoomMediaAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_edit_room_shows_existing_photo_and_video_previews(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('rooms/main.jpg', 'fake image');
        Storage::disk('public')->put('rooms/gallery/gallery.jpg', 'fake gallery image');
        Storage::disk('public')->put('rooms/videos/demo.mp4', 'fake video');

        $admin = User::create([
            'nama_lengkap' => 'Admin Media',
            'email' => 'admin.media@example.test',
            'password_hash' => Hash::make('password'),
            'no_telepon' => '081111111111',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $room = Room::create([
            'nomor_kamar' => 'A-404',
            'lantai' => 4,
            'deskripsi' => 'Kamar dengan media existing',
            'harga_harian' => 200000,
            'harga_bulanan' => 2000000,
            'deposit' => 500000,
            'status' => 'tersedia',
            'foto_utama' => 'rooms/main.jpg',
            'gallery_images' => ['rooms/gallery/gallery.jpg'],
            'video_path' => 'rooms/videos/demo.mp4',
            'is_published' => true,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(EditRoom::class, ['record' => $room->getRouteKey()])
            ->assertSee('Media Saat Ini')
            ->assertSee('/storage/rooms/main.jpg', false)
            ->assertSee('/storage/rooms/gallery/gallery.jpg', false)
            ->assertSee('/storage/rooms/videos/demo.mp4', false)
            ->assertSee('Video kamar');

        $fields = $component->instance()->form->getFlatFields(withHidden: true);

        $this->assertSame('/storage/rooms/main.jpg', array_values($fields['foto_utama']->getUploadedFiles())[0]['url']);
        $this->assertSame('/storage/rooms/gallery/gallery.jpg', array_values($fields['gallery_images']->getUploadedFiles())[0]['url']);
        $this->assertSame('/storage/rooms/videos/demo.mp4', array_values($fields['video_path']->getUploadedFiles())[0]['url']);
        $this->assertGreaterThan(0, array_values($fields['foto_utama']->getUploadedFiles())[0]['size']);
    }
}
