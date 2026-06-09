<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPlatformResource\Pages;
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

class ContactPlatformResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'Platform Digital';

    protected static ?string $modelLabel = 'Platform Digital';

    protected static ?string $pluralModelLabel = 'Platform Digital';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 32;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-share';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Platform Digital')
                ->columns(2)
                ->schema([
                    TextInput::make('platform_title')->label('Judul')->maxLength(255),
                    Textarea::make('platform_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Repeater::make('platform_links')
                        ->label('Tautan Platform')
                        ->schema([
                            TextInput::make('label')->label('Nama Platform')->required(),
                            TextInput::make('url')->label('Tautan')->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform_title')->label('Judul Platform')->limit(50),
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
            'index' => Pages\ListContactPlatforms::route('/'),
            'edit' => Pages\EditContactPlatform::route('/{record}/edit'),
        ];
    }
}
