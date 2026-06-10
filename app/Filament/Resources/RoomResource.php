<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\RoomStatus;
use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers\ReviewsRelationManager;
use App\Filament\Support\PublicFileUpload;
use App\Models\Room;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationLabel = 'Kamar';

    protected static ?string $modelLabel = 'Kamar';

    protected static ?string $pluralModelLabel = 'Daftar Kamar';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Utama';

    protected static ?int $navigationSort = 1;

    /**
     * Override via method — kompatibel dengan Filament v5 + PHP 8.3.
     * Parent menggunakan BackedEnum|string|null sehingga tidak bisa dioverride
     * dengan ?string di child class.
     */
    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-building-office-2';
    }

    // ─── Form (Filament v5: Schema) ───────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Dasar')
                ->description('Identitas dan spesifikasi fisik kamar.')
                ->icon('heroicon-o-information-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('nomor_kamar')
                        ->label('Nomor Kamar')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->placeholder('Contoh: A-101'),

                    TextInput::make('lantai')
                        ->label('Lantai')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(99)
                        ->placeholder('1'),

                    TextInput::make('luas_m2')
                        ->label('Luas Kamar (m²)')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('m²')
                        ->placeholder('20'),

                    Select::make('status')
                        ->label('Status Kamar')
                        ->required()
                        ->options(RoomStatus::class)
                        ->default(RoomStatus::Tersedia)
                        ->native(false),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull()
                        ->placeholder('Deskripsi singkat tentang kamar ini…'),
                ]),

            Section::make('Harga & Deposit')
                ->description('Informasi tarif sewa dan deposit.')
                ->icon('heroicon-o-currency-dollar')
                ->columns(3)
                ->schema([
                    TextInput::make('harga_harian')
                        ->label('Harga Harian')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('150000'),

                    TextInput::make('harga_bulanan')
                        ->label('Harga Bulanan')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('3000000'),

                    TextInput::make('deposit')
                        ->label('Deposit')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->default(0)
                        ->placeholder('500000'),
                ]),

            Section::make('Fasilitas & Media')
                ->description('Fasilitas kamar, foto, galeri, dan video.')
                ->icon('heroicon-o-sparkles')
                ->columns(1)
                ->schema([
                    TagsInput::make('fasilitas')
                        ->label('Fasilitas')
                        ->placeholder('Tambah fasilitas, tekan Enter…')
                        ->suggestions([
                            'WiFi',
                            'AC',
                            'Pemanas Air',
                            'TV',
                            'Kulkas',
                            'Kamar Mandi Dalam',
                            'Balkon',
                            'Parkir',
                            'Dapur',
                            'Meja Belajar',
                        ])
                        ->helperText('Ketik fasilitas lalu tekan Enter untuk menambahkan.'),

                    Placeholder::make('media_preview')
                        ->label('Media Saat Ini')
                        ->content(fn (?Room $record): HtmlString => self::mediaPreview($record))
                        ->columnSpanFull(),

                    FileUpload::make('foto_utama')
                        ->label('Foto Utama Kamar')
                        ->image()
                        ->disk('public')
                        ->directory('rooms')
                        ->visibility('public')
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                        ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->imageEditor()
                        ->imagePreviewHeight('180')
                        ->panelLayout('integrated')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Format: JPG, PNG, WebP. Maks. 2 MB.'),

                    FileUpload::make('gallery_images')
                        ->label('Galeri Foto Kamar')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->disk('public')
                        ->directory('rooms/gallery')
                        ->visibility('public')
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                        ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->imageEditor()
                        ->imagePreviewHeight('140')
                        ->panelLayout('grid')
                        ->maxFiles(10)
                        ->maxSize(4096)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Tambahkan beberapa foto kamar. Format: JPG, PNG, WebP. Maks. 4 MB per file.'),

                    FileUpload::make('video_path')
                        ->label('Video Kamar')
                        ->disk('public')
                        ->directory('rooms/videos')
                        ->visibility('public')
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                        ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->maxSize(51200)
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->helperText('Opsional. Format: MP4, WebM, atau OGG. Maks. 50 MB.'),

                    Toggle::make('is_published')
                        ->label('Tampilkan di Landing Page')
                        ->helperText('Jika aktif, kamar ini akan muncul di halaman publik.')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ]),

            Section::make('Statistik Ulasan')
                ->columns(2)
                ->schema([
                    Placeholder::make('rating_rata_rata')
                        ->label('Rating Rata-rata')
                        ->content(fn (?Room $record): string => $record
                            ? number_format((float) ($record->reviews()->avg('rating') ?? 0), 1, ',', '.') . ' / 5'
                            : '0,0 / 5'),

                    Placeholder::make('jumlah_ulasan')
                        ->label('Jumlah Ulasan')
                        ->content(fn (?Room $record): string => $record
                            ? (string) $record->reviews()->count()
                            : '0'),
                ]),
        ]);
    }

    // ─── Table ───────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_kamar')
                    ->label('No. Kamar')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('lantai')
                    ->label('Lantai')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('luas_m2')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->alignRight(),

                Tables\Columns\TextColumn::make('harga_harian')
                    ->label('Harga Harian')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight(),

                Tables\Columns\TextColumn::make('harga_bulanan')
                    ->label('Harga Bulanan')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\ImageColumn::make('foto_utama')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('gallery_images')
                    ->label('Galeri')
                    ->formatStateUsing(fn (mixed $state): string => is_array($state) ? count($state) . ' foto' : '0 foto')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('video_path')
                    ->label('Video')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nomor_kamar')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kamar')
                    ->options(RoomStatus::class)
                    ->native(false),

                SelectFilter::make('lantai')
                    ->label('Lantai')
                    ->options(fn (): array => Room::query()
                        ->orderBy('lantai')
                        ->distinct()
                        ->pluck('lantai', 'lantai')
                        ->mapWithKeys(fn ($lantai) => [$lantai => "Lantai {$lantai}"])
                        ->all()
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Dipublikasikan')
                    ->falseLabel('Disembunyikan'),
            ])
            ->actions([
                // Di Filament v5, ViewAction dihapus dari Tables — gunakan EditAction saja
                // Row klik otomatis membuka edit karena tidak ada 'view' page
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ])
            ->recordUrl(fn (Room $record): string => static::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum Ada Kamar')
            ->emptyStateDescription('Klik tombol "Tambah Kamar" untuk mulai menambahkan data kamar.');
    }

    // ─── Authorization ────────────────────────────────────────────────────────
    // Filament v5 otomatis memeriksa Gate/Policy yang telah didaftarkan.
    // Gate::policy(Room::class, RoomPolicy::class) di AppServiceProvider cukup.

    // ─── Relations & Pages ───────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit'   => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    /**
     * Di panel admin semua kamar ditampilkan tanpa filter is_published.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    private static function mediaPreview(?Room $record): HtmlString
    {
        if (! $record) {
            return new HtmlString('<span style="color:#6b7280">Simpan kamar terlebih dahulu untuk melihat pratinjau media.</span>');
        }

        $items = [];

        if ($record->foto_utama) {
            $items[] = self::imagePreviewItem('Foto utama', $record->foto_utama);
        }

        foreach (($record->gallery_images ?? []) as $index => $path) {
            if (! is_string($path) || trim($path) === '') {
                continue;
            }

            $items[] = self::imagePreviewItem('Galeri '.($index + 1), $path);
        }

        if ($record->video_path) {
            $items[] = self::videoPreviewItem('Video kamar', $record->video_path);
        }

        if ($items === []) {
            return new HtmlString('<span style="color:#6b7280">Belum ada foto atau video untuk kamar ini.</span>');
        }

        return new HtmlString(
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px">'
            .implode('', $items)
            .'</div>'
        );
    }

    private static function imagePreviewItem(string $label, string $path): string
    {
        $url = self::publicFileUrl($path);
        $escapedUrl = e($url);
        $escapedLabel = e($label);

        return '<figure style="margin:0;display:grid;gap:8px">'
            .'<img src="'.$escapedUrl.'" alt="'.$escapedLabel.'" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb">'
            .'<figcaption style="display:flex;justify-content:space-between;gap:8px;font-size:12px;color:#4b5563">'
            .'<span>'.$escapedLabel.'</span>'
            .'<a href="'.$escapedUrl.'" target="_blank" rel="noopener noreferrer" style="color:#2563eb;font-weight:600;text-decoration:underline">Buka</a>'
            .'</figcaption>'
            .'</figure>';
    }

    private static function videoPreviewItem(string $label, string $path): string
    {
        $url = self::publicFileUrl($path);
        $escapedUrl = e($url);
        $escapedLabel = e($label);

        return '<figure style="margin:0;display:grid;gap:8px">'
            .'<video src="'.$escapedUrl.'" controls preload="metadata" style="width:100%;max-height:180px;border-radius:8px;border:1px solid #e5e7eb;background:#111827"></video>'
            .'<figcaption style="display:flex;justify-content:space-between;gap:8px;font-size:12px;color:#4b5563">'
            .'<span>'.$escapedLabel.'</span>'
            .'<a href="'.$escapedUrl.'" target="_blank" rel="noopener noreferrer" style="color:#2563eb;font-weight:600;text-decoration:underline">Buka</a>'
            .'</figcaption>'
            .'</figure>';
    }

    /**
     * Filament's default upload metadata URL follows the disk URL from APP_URL.
     * In local admin sessions the app is often opened on 127.0.0.1 with a port,
     * so a relative URL keeps FilePond on the same origin and prevents
     * "Waiting for size" from hanging forever.
     *
     * @param  string|array<string, string>|null  $storedFileNames
     * @return array{name: string, size: int, type: ?string, url: string}
     */
    private static function uploadedFileMeta(BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array
    {
        $storage = $component->getDisk();

        try {
            if ($component->shouldFetchFileInformation() && ! $storage->exists($file)) {
                return null;
            }

            $size = $component->shouldFetchFileInformation() ? $storage->size($file) : 0;
            $type = $component->shouldFetchFileInformation() ? $storage->mimeType($file) : null;
        } catch (\Throwable) {
            return null;
        }

        $name = basename($file);

        if ($component->isMultiple() && is_array($storedFileNames)) {
            $name = $storedFileNames[$file] ?? $name;
        } elseif (is_string($storedFileNames)) {
            $name = $storedFileNames;
        }

        return [
            'name' => $name,
            'size' => $size,
            'type' => $type,
            'url' => self::publicFileUrl($file),
        ];
    }

    private static function publicFileUrl(string $path): string
    {
        if (trim($path) === '') {
            return '#';
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return PublicFileUpload::url($path) ?? '#';
    }
}
