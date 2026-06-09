<?php

declare(strict_types=1);

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Daftar Ulasan';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Pengguna')
                    ->searchable()
                    ->placeholder('Pengguna Berlima'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (mixed $state): string => "{$state}/5")
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(90)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Ulasan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Ulasan')
            ->emptyStateDescription('Ulasan pengguna untuk kamar ini akan tampil di sini.');
    }
}
