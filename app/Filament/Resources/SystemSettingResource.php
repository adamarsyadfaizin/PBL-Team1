<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Filament\Support\PublicFileUpload;
use App\Models\Room;
use App\Models\SystemSetting;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationLabel = 'Pengaturan Beranda';

    protected static ?string $modelLabel = 'Pengaturan Beranda';

    protected static ?string $pluralModelLabel = 'Pengaturan Beranda';

    protected static ?int $navigationSort = 9;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero')
                ->columns(2)
                ->schema([
                    TextInput::make('hero_label')->label('Label Hero')->maxLength(255),
                    Textarea::make('hero_title')->label('Judul Hero')->rows(3)->required(),
                    FileUpload::make('hero_image')
                        ->label('Foto Hero Beranda')
                        ->image()
                        ->disk('public')
                        ->directory('home/hero')
                        ->visibility('public')
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->fetchFileInformation(false)
                        ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                        ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->imageEditor()
                        ->imagePreviewHeight('220')
                        ->panelLayout('integrated')
                        ->maxSize(4096)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Foto ini khusus untuk hero section Beranda. Format: JPG, PNG, WebP. Maks. 4 MB.')
                        ->columnSpanFull(),
                    Textarea::make('hero_description')->label('Deskripsi Hero')->rows(3)->columnSpanFull(),
                ]),

            Section::make('Cara Pemesanan')
                ->columns(2)
                ->schema([
                    TextInput::make('how_label')->label('Label Bagian')->maxLength(255),
                    Textarea::make('how_title')->label('Judul Bagian')->rows(2)->required(),
                    Textarea::make('how_description')->label('Deskripsi Bagian')->rows(3)->columnSpanFull(),
                    TextInput::make('how_step_1_title')->label('Judul Langkah 1')->maxLength(255),
                    Textarea::make('how_step_1_description')->label('Deskripsi Langkah 1')->rows(3),
                    TextInput::make('how_step_2_title')->label('Judul Langkah 2')->maxLength(255),
                    Textarea::make('how_step_2_description')->label('Deskripsi Langkah 2')->rows(3),
                    TextInput::make('how_step_3_title')->label('Judul Langkah 3')->maxLength(255),
                    Textarea::make('how_step_3_description')->label('Deskripsi Langkah 3')->rows(3),
                ]),

            Section::make('Kamar Pilihan')
                ->columns(2)
                ->schema([
                    TextInput::make('rooms_label')->label('Label Bagian')->maxLength(255),
                    Textarea::make('rooms_title')->label('Judul Bagian')->rows(2)->required(),
                    Textarea::make('rooms_description')->label('Deskripsi Bagian')->rows(3)->columnSpanFull(),
                    Select::make('highlight_room_ids')
                        ->label('Kamar Highlight Beranda')
                        ->helperText('Pilih maksimal 3 kamar yang akan tampil di section kamar unggulan Beranda.')
                        ->multiple()
                        ->maxItems(3)
                        ->options(fn (): array => Room::query()
                            ->where('is_published', true)
                            ->orderBy('nomor_kamar')
                            ->get()
                            ->mapWithKeys(fn (Room $room): array => [
                                $room->id => 'Kamar ' . $room->nomor_kamar . ($room->tipe_kamar ? ' - ' . $room->tipe_kamar : ''),
                            ])
                            ->all())
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Sekilas Suasana')
                ->columns(2)
                ->schema([
                    TextInput::make('gallery_label')->label('Label Bagian')->maxLength(255),
                    Textarea::make('gallery_title')->label('Judul Bagian')->rows(2)->required(),
                    Textarea::make('gallery_description')->label('Deskripsi Bagian')->rows(3)->columnSpanFull(),
                ]),

            Section::make('Hubungi Kami')
                ->columns(2)
                ->schema([
                    TextInput::make('facilities_label')->label('Label Bagian')->maxLength(255),
                    Textarea::make('facilities_title')->label('Judul Bagian')->rows(2)->required(),
                    Textarea::make('facilities_description')->label('Deskripsi Bagian')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hero_title')
                    ->label('Hero')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rooms_title')
                    ->label('Kamar Pilihan')
                    ->limit(40),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->recordUrl(fn (SystemSetting $record): string => static::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum Ada Pengaturan')
            ->emptyStateDescription('Buat satu pengaturan untuk mengatur teks halaman utama.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereKey(SystemSetting::HOME_KEY);
    }
}
