<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactLocationResource\Pages;
use App\Models\GuestProfile;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContactLocationResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'Lokasi Kontak';

    protected static ?string $modelLabel = 'Lokasi Kontak';

    protected static ?string $pluralModelLabel = 'Lokasi Kontak';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 33;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-map-pin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lokasi')
                ->columns(2)
                ->schema([
                    TextInput::make('location_label')->label('Label')->maxLength(255),
                    TextInput::make('location_title')->label('Judul')->maxLength(255),
                    Textarea::make('location_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Textarea::make('location_embed_url')->label('Google Maps Embed URL')->rows(3)->columnSpanFull(),
                    TextInput::make('location_name')->label('Nama Lokasi')->maxLength(255),
                    TextInput::make('location_address')->label('Alamat')->maxLength(255),
                    TextInput::make('location_google_maps_url')->label('Tautan Google Maps')->maxLength(255),
                    TextInput::make('location_waze_url')->label('Tautan Waze')->maxLength(255),
                    Repeater::make('location_notes')
                        ->label('Catatan Lokasi')
                        ->simple(TextInput::make('note')->required())
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location_name')->label('Nama Lokasi')->limit(40),
                Tables\Columns\TextColumn::make('location_address')->label('Alamat')->limit(50),
                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diubah')->dateTime('d M Y H:i'),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
            ])
            ->recordUrl(fn (GuestProfile $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactLocations::route('/'),
            'edit' => Pages\EditContactLocation::route('/{record}/edit'),
        ];
    }
}
