<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactFeedbackResource\Pages;
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

class ContactFeedbackResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'Umpan Balik Kontak';

    protected static ?string $modelLabel = 'Umpan Balik Kontak';

    protected static ?string $pluralModelLabel = 'Umpan Balik Kontak';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 34;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Teks Umpan Balik')
                ->columns(2)
                ->schema([
                    TextInput::make('feedback_title')->label('Judul')->maxLength(255),
                    TextInput::make('feedback_yes_label')->label('Label Ya')->maxLength(255),
                    TextInput::make('feedback_no_label')->label('Label Tidak')->maxLength(255),
                    TextInput::make('feedback_wa_label')->label('Label Tombol WhatsApp')->maxLength(255),
                    Textarea::make('feedback_prompt')->label('Pertanyaan Masukan')->rows(2)->columnSpanFull(),
                    TextInput::make('feedback_help_title')->label('Judul Bantuan Lain')->maxLength(255),
                ]),

            Section::make('Masukan Pengunjung')
                ->schema([
                    Repeater::make('masukkan')
                        ->label('Masukan')
                        ->schema([
                            Textarea::make('message')->label('Masukan')->rows(3)->disabled(),
                            TextInput::make('created_at')->label('Dikirim Pada')->disabled(),
                        ])
                        ->columns(2)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('feedback_title')->label('Judul Umpan Balik')->limit(50),
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
            'index' => Pages\ListContactFeedback::route('/'),
            'edit' => Pages\EditContactFeedback::route('/{record}/edit'),
        ];
    }
}
