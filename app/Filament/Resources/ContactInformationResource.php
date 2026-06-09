<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactInformationResource\Pages;
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

class ContactInformationResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'Informasi Kontak';

    protected static ?string $modelLabel = 'Informasi Kontak';

    protected static ?string $pluralModelLabel = 'Informasi Kontak';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 30;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-phone';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Intro Kontak')
                ->columns(2)
                ->schema([
                    TextInput::make('contact_label')->label('Label')->maxLength(255),
                    TextInput::make('contact_title')->label('Judul')->maxLength(255),
                    Textarea::make('contact_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    TextInput::make('contact_button_label')->label('Label Tombol')->maxLength(255),
                ]),

            Section::make('Informasi Kontak')
                ->schema([
                    Repeater::make('contact_items')
                        ->label('Daftar Informasi Kontak')
                        ->schema([
                            TextInput::make('label')->label('Label')->required(),
                            TextInput::make('value')->label('Nilai')->required(),
                            TextInput::make('url')->label('Tautan')->maxLength(255),
                        ])
                        ->columns(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_title')->label('Judul Kontak')->limit(50),
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
            'index' => Pages\ListContactInformation::route('/'),
            'edit' => Pages\EditContactInformation::route('/{record}/edit'),
        ];
    }
}
