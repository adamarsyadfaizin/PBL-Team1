<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactFaqResource\Pages;
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

class ContactFaqResource extends Resource
{
    protected static ?string $model = GuestProfile::class;

    protected static ?string $navigationLabel = 'FAQ Kontak';

    protected static ?string $modelLabel = 'FAQ Kontak';

    protected static ?string $pluralModelLabel = 'FAQ Kontak';

    protected static string|\UnitEnum|null $navigationGroup = 'Kontak Website';

    protected static ?int $navigationSort = 31;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-question-mark-circle';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pertanyaan Umum')
                ->columns(2)
                ->schema([
                    TextInput::make('contact_faq_label')->label('Label')->maxLength(255),
                    TextInput::make('contact_faq_title')->label('Judul')->maxLength(255),
                    Textarea::make('contact_faq_description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Repeater::make('contact_faqs')
                        ->label('Daftar FAQ')
                        ->schema([
                            TextInput::make('question')->label('Pertanyaan')->required(),
                            Textarea::make('answer')->label('Jawaban')->rows(3)->required(),
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
                Tables\Columns\TextColumn::make('contact_faq_title')->label('Judul FAQ')->limit(50),
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
            'index' => Pages\ListContactFaqs::route('/'),
            'edit' => Pages\EditContactFaq::route('/{record}/edit'),
        ];
    }
}
