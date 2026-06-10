<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GuestProfileResource\Pages;
use App\Filament\Support\PublicFileUpload;
use App\Models\GuestProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GuestProfileResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'Profil Guest House';

    protected static ?string $modelLabel = 'Profil Guest House';

    protected static ?string $pluralModelLabel = 'Profil Guest House';

    protected static ?int $navigationSort = 8;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-home-modern';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tentang Kami')
                ->columns(2)
                ->schema([
                    TextInput::make('eyebrow')->label('Label')->maxLength(255),
                    TextInput::make('name')->label('Nama Guest House')->required()->maxLength(255),
                    Textarea::make('description')->label('Deskripsi')->rows(5)->required()->columnSpanFull(),
                    FileUpload::make('main_photo')
                        ->label('Foto Utama')
                        ->image()
                        ->disk('public')
                        ->directory('guest-profile')
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
                        ->helperText('Format: JPG, PNG, WebP. Maks. 4 MB.')
                        ->columnSpanFull(),
                ]),

            Section::make('Cerita Singkat')
                ->schema([
                    Repeater::make('stories')
                        ->label('Cerita')
                        ->schema([
                            TextInput::make('title')->label('Judul')->required(),
                            Textarea::make('description')->label('Deskripsi')->rows(3)->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(2),
                ]),

            Section::make('Komitmen')
                ->columns(2)
                ->schema([
                    TextInput::make('commitment_label')->label('Label')->maxLength(255),
                    TextInput::make('commitment_title')->label('Judul')->maxLength(255),
                    Repeater::make('commitments')
                        ->label('Daftar Komitmen')
                        ->schema([
                            TextInput::make('title')->label('Judul')->required(),
                            Textarea::make('description')->label('Deskripsi')->rows(3)->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Informasi Penting')
                ->columns(2)
                ->schema([
                    TextInput::make('important_label')->label('Label')->maxLength(255),
                    TextInput::make('important_title')->label('Judul')->maxLength(255),
                    Textarea::make('important_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Repeater::make('important_items')
                        ->label('Daftar Informasi')
                        ->schema([
                            TextInput::make('title')->label('Judul')->required(),
                            Textarea::make('description')->label('Deskripsi')->rows(3)->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Galeri')
                ->columns(2)
                ->schema([
                    TextInput::make('gallery_label')->label('Label')->maxLength(255),
                    TextInput::make('gallery_title')->label('Judul')->maxLength(255),
                    Textarea::make('gallery_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Repeater::make('gallery_items')
                        ->label('Foto Galeri')
                        ->schema([
                            TextInput::make('title')->label('Judul Foto')->required(),
                            FileUpload::make('image')
                                ->label('Gambar')
                                ->image()
                                ->disk('public')
                                ->directory('guest-gallery')
                                ->visibility('public')
                                ->openable()
                                ->downloadable()
                                ->previewable()
                                ->fetchFileInformation(false)
                                ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array => PublicFileUpload::uploadedFileMeta($component, $file, $storedFileNames))
                                ->getOpenableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                                ->getDownloadableFileUrlUsing(fn (string $file): ?string => PublicFileUpload::url($file))
                                ->imageEditor()
                                ->imagePreviewHeight('140')
                                ->panelLayout('integrated')
                                ->maxSize(4096)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->required(),
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
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('gallery_title')->label('Galeri')->limit(40),
                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diubah')->dateTime('d M Y H:i'),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->recordUrl(fn (GuestProfile $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuestProfiles::route('/'),
            'create' => Pages\CreateGuestProfile::route('/create'),
            'edit' => Pages\EditGuestProfile::route('/{record}/edit'),
        ];
    }
}
