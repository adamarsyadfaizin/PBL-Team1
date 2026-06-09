<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GuestRequestResource\Pages;
use App\Models\GuestRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class GuestRequestResource extends Resource
{
    protected static ?string $model = GuestRequest::class;

    protected static ?string $navigationLabel = 'Permintaan Tamu';

    protected static ?string $modelLabel = 'Permintaan Tamu';

    protected static ?string $pluralModelLabel = 'Permintaan Tamu';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 35;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-inbox';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Status')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->required()
                        ->native(false),
                ]),

            Section::make('Data Pengirim')
                ->columns(2)
                ->schema([
                    TextInput::make('nama')->label('Nama')->disabled()->dehydrated(false),
                    TextInput::make('phone')->label('No. WhatsApp / Ponsel')->disabled()->dehydrated(false),
                    TextInput::make('email')->label('Post-el')->disabled()->dehydrated(false),
                    TextInput::make('request_type')->label('Jenis Permintaan')->disabled()->dehydrated(false),
                ]),

            Section::make('Detail Permintaan')
                ->columns(2)
                ->schema([
                    DatePicker::make('checkin')->label('Tanggal Masuk')->disabled()->dehydrated(false),
                    DatePicker::make('checkout')->label('Tanggal Keluar')->disabled()->dehydrated(false),
                    TextInput::make('tipe_kamar')->label('Tipe Kamar')->disabled()->dehydrated(false),
                    TextInput::make('tipe_sewa')->label('Tipe Sewa')->disabled()->dehydrated(false),
                    TextInput::make('jumlah_tamu')->label('Jumlah Tamu')->disabled()->dehydrated(false),
                    TextInput::make('complaint_category')->label('Kategori Keluhan')->disabled()->dehydrated(false),
                    Textarea::make('pesan')->label('Pesan / Catatan')->rows(4)->disabled()->dehydrated(false)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('phone')->label('WhatsApp')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Post-el')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('request_type')->label('Jenis')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'diarsipkan' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dikirim')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Status')->options(self::statusOptions())->native(false),
                SelectFilter::make('request_type')
                    ->label('Jenis Permintaan')
                    ->options([
                        'availability' => 'Cek Ketersediaan Kamar',
                        'booking' => 'Pemesanan Kamar',
                        'reschedule' => 'Perubahan Jadwal Menginap',
                        'cancel' => 'Pembatalan Pemesanan',
                        'complaint' => 'Keluhan / Bantuan',
                        'general' => 'Pertanyaan Umum',
                    ])
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
            ->recordUrl(fn (GuestRequest $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'diarsipkan' => 'Diarsipkan',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuestRequests::route('/'),
            'edit' => Pages\EditGuestRequest::route('/{record}/edit'),
        ];
    }
}
