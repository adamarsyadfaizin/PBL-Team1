<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Support\PublicFileUpload;
use App\Models\Booking;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationLabel = 'Pemesanan';

    protected static ?string $modelLabel = 'Pemesanan';

    protected static ?string $pluralModelLabel = 'Data Pemesanan';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Utama';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'menunggu_konfirmasi'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pemesanan')
                ->columns(2)
                ->schema([
                    TextInput::make('kode_booking')
                        ->label('Kode Pemesanan')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options(self::statusOptions())
                        ->native(false),

                    Select::make('room_id')
                        ->label('Kamar')
                        ->relationship('room', 'nomor_kamar')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('user_id')
                        ->label('Penyewa')
                        ->relationship('user', 'nama_lengkap')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('tipe_sewa')
                        ->label('Tipe Sewa')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('durasi')
                        ->label('Durasi')
                        ->disabled()
                        ->dehydrated(false),

                    DatePicker::make('tanggal_check_in')
                        ->label('Tanggal Masuk')
                        ->disabled()
                        ->dehydrated(false),

                    DatePicker::make('tanggal_check_out')
                        ->label('Tanggal Keluar')
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make('Pembayaran')
                ->columns(2)
                ->schema([
                    TextInput::make('harga_snapshot')
                        ->label('Harga Saat Dipesan')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('total_tagihan')
                        ->label('Total Tagihan')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false),

                    FileUpload::make('bukti_transfer')
                        ->label('Bukti Transfer')
                        ->disk('public')
                        ->directory('booking-payment-proofs')
                        ->downloadable()
                        ->openable()
                        ->previewable()
                        ->fetchFileInformation(false)
                        ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                        ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Placeholder::make('bukti_transfer_preview')
                        ->label('Preview Bukti Transfer')
                        ->content(fn (?Booking $record): HtmlString => self::paymentProofPreview($record))
                        ->columnSpanFull(),
                ]),

            Section::make('Catatan')
                ->schema([
                    Textarea::make('catatan_penyewa')
                        ->label('Catatan Penyewa')
                        ->rows(4)
                        ->disabled()
                        ->dehydrated(false),

                    Textarea::make('alasan_pembatalan')
                        ->label('Alasan Pembatalan')
                        ->rows(3)
                        ->maxLength(1000)
                        ->placeholder('Isi jika status pemesanan dibatalkan.'),
                ]),

            Section::make('Konfirmasi Admin')
                ->columns(2)
                ->schema([
                    Select::make('dikonfirmasi_oleh')
                        ->label('Dikonfirmasi Oleh')
                        ->relationship('confirmedBy', 'nama_lengkap')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('tanggal_konfirmasi')
                        ->label('Tanggal Konfirmasi')
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_booking')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('room.nomor_kamar')
                    ->label('Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Penyewa')
                    ->searchable()
                    ->limit(28),

                Tables\Columns\TextColumn::make('tanggal_check_in')
                    ->label('Masuk')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_check_out')
                    ->label('Keluar')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bukti_transfer')
                    ->label('Bukti TF')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Lihat bukti' : 'Belum ada')
                    ->url(fn (?string $state): ?string => self::publicFileUrl($state))
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn (?string $state): string => filled($state) ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'menunggu_konfirmasi' => 'warning',
                        'active_stay' => 'success',
                        'selesai' => 'info',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('confirmedBy.nama_lengkap')
                    ->label('Dikonfirmasi Oleh')
                    ->limit(28)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_konfirmasi')
                    ->label('Tanggal Konfirmasi')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::statusOptions())
                    ->native(false),
            ])
            ->actions([
                EditAction::make()->label('Detail / Ubah Status'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ])
            ->recordUrl(fn (Booking $record): string => static::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum Ada Pemesanan')
            ->emptyStateDescription('Pemesanan dari pengguna akan tampil di halaman ini.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['room', 'user', 'confirmedBy']);
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu Data',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'active_stay' => 'Berhasil / Sedang Menginap',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    private static function publicFileUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return PublicFileUpload::url($path);
    }

    private static function paymentProofPreview(?Booking $record): HtmlString
    {
        $path = $record?->bukti_transfer;
        $url = self::publicFileUrl($path);

        if ($url === null) {
            return new HtmlString('<span style="color:#6b7280">Belum ada bukti transfer.</span>');
        }

        $escapedUrl = e($url);
        $fileName = e(basename((string) $path));
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $openLink = '<a href="'.$escapedUrl.'" target="_blank" rel="noopener noreferrer" style="color:#2563eb;font-weight:600;text-decoration:underline">Buka / unduh bukti transfer</a>';

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return new HtmlString(
                '<div style="display:grid;gap:10px">'
                .'<img src="'.$escapedUrl.'" alt="Bukti transfer '.$fileName.'" style="max-width:360px;width:100%;border-radius:10px;border:1px solid #e5e7eb">'
                .'<div>'.$openLink.'</div>'
                .'</div>'
            );
        }

        return new HtmlString('<div style="display:grid;gap:6px"><strong>'.$fileName.'</strong>'.$openLink.'</div>');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
