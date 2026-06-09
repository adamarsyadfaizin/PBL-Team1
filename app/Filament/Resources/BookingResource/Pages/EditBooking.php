<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookingResource\Pages;

use App\Enums\RoomStatus;
use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Pemesanan'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Booking $booking */
        $booking = $this->record;

        if (
            ($data['status'] ?? null) === 'active_stay'
            && ($booking->status !== 'active_stay' || blank($booking->tanggal_konfirmasi))
        ) {
            $data['dikonfirmasi_oleh'] = Auth::id();
            $data['tanggal_konfirmasi'] = now();
        }

        if (($data['status'] ?? null) !== 'dibatalkan') {
            $data['alasan_pembatalan'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Booking $booking */
        $booking = $this->record->refresh();
        $room = $booking->room;

        if (! $room) {
            return;
        }

        if ($booking->status === 'active_stay') {
            $room->forceFill(['status' => RoomStatus::Terisi])->save();

            return;
        }

        if (! in_array($booking->status, ['selesai', 'dibatalkan'], true)) {
            return;
        }

        $hasOtherActiveBooking = Booking::query()
            ->where('room_id', $room->id)
            ->where('id', '!=', $booking->id)
            ->where('status', 'active_stay')
            ->exists();

        if (! $hasOtherActiveBooking && ($room->status->value ?? $room->status) === RoomStatus::Terisi->value) {
            $room->forceFill(['status' => RoomStatus::Tersedia])->save();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pemesanan berhasil diperbarui';
    }
}
