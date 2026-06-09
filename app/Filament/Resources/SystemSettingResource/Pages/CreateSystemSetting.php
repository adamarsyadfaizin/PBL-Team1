<?php

declare(strict_types=1);

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use App\Models\SystemSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemSetting extends CreateRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $highlightRoomIds = $this->data['highlight_room_ids'] ?? [];

        return array_merge([
            'key' => SystemSetting::HOME_KEY,
            'value' => (new SystemSetting())->highlightRoomValue($highlightRoomIds),
            'deskripsi' => 'Pengaturan konten landing page.',
        ], SystemSetting::defaults(), $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengaturan home berhasil dibuat';
    }
}
