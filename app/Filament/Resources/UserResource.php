<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Daftar Pengguna';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Akun')
                ->description('Data utama pengguna yang terdaftar.')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Contoh: Budi Santoso'),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->placeholder('contoh@email.com'),

                    TextInput::make('phone')
                        ->label('No. Telepon')
                        ->tel()
                        ->maxLength(20)
                        ->placeholder('08xxxxxxxxxx'),

                    TextInput::make('ktp')
                        ->label('No. KTP')
                        ->maxLength(16)
                        ->minLength(16)
                        ->placeholder('16 digit nomor KTP')
                        ->helperText('Harus tepat 16 digit angka.'),

                    Select::make('role')
                        ->label('Role')
                        ->required()
                        ->options([
                            'user'  => 'User',
                            'admin' => 'Admin',
                        ])
                        ->default('user')
                        ->native(false),
                ]),

            Section::make('Keamanan')
                ->description('Atur password pengguna. Kosongkan jika tidak ingin mengubah.')
                ->icon('heroicon-o-lock-closed')
                ->columns(1)
                ->schema([
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->maxLength(255)
                        ->placeholder('Kosongkan jika tidak ingin mengubah')
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->helperText('Minimal 8 karakter. Biarkan kosong saat edit jika tidak ingin mengganti password.'),
                ]),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email disalin!'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ktp')
                    ->label('No. KTP')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'warning' => 'admin',
                        'primary' => 'user',
                    ])
                    ->icons([
                        'heroicon-o-cog-6-tooth' => 'admin',
                        'heroicon-o-user'        => 'user',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'user'  => 'User',
                        'admin' => 'Admin',
                    ])
                    ->native(false),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ])
            ->recordUrl(fn (User $record): string => static::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum Ada Pengguna')
            ->emptyStateDescription('Klik tombol "Tambah Pengguna" untuk menambahkan akun baru.');
    }

    // ─── Relations & Pages ────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
