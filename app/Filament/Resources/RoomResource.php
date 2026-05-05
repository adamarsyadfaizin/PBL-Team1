<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\RoomStatus;
use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationLabel = 'Kamar';

    protected static ?string $modelLabel = 'Kamar';

    protected static ?string $pluralModelLabel = 'Daftar Kamar';

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
                ->description('Fasilitas kamar dan foto utama.')
                ->icon('heroicon-o-sparkles')
                ->columns(1)
                ->schema([
                    TagsInput::make('fasilitas')
                        ->label('Fasilitas')
                        ->placeholder('Tambah fasilitas, tekan Enter…')
                        ->suggestions([
                            'WiFi',
                            'AC',
                            'Water Heater',
                            'TV',
                            'Kulkas',
                            'Kamar Mandi Dalam',
                            'Balkon',
                            'Parkir',
                            'Dapur',
                            'Meja Belajar',
                        ])
                        ->helperText('Ketik fasilitas lalu tekan Enter untuk menambahkan.'),

                    FileUpload::make('foto_utama')
                        ->label('Foto Utama Kamar')
                        ->image()
                        ->disk('public')
                        ->directory('rooms')
                        ->imageEditor()
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Format: JPG, PNG, WebP. Maks. 2 MB.'),

                    Toggle::make('is_published')
                        ->label('Tampilkan di Landing Page')
                        ->helperText('Jika aktif, kamar ini akan muncul di halaman publik.')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
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
                    ->label('Published')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\ImageColumn::make('foto_utama')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),

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
        return [];
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
}
