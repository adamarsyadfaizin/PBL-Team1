<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            // Primary Key — UUID
            $table->uuid('id')->primary();

            // Identitas kamar
            $table->string('nomor_kamar')->unique();
            $table->smallInteger('lantai');
            $table->decimal('luas_m2', 8, 2)->nullable();
            $table->text('deskripsi')->nullable();

            // Fasilitas disimpan sebagai JSON array
            $table->jsonb('fasilitas')->nullable();

            // Harga
            $table->decimal('harga_harian', 12, 2);
            $table->decimal('harga_bulanan', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);

            // Status & visibilitas
            $table->enum('status', ['tersedia', 'terisi', 'perbaikan'])->default('tersedia');
            $table->string('foto_utama')->nullable();
            $table->boolean('is_published')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
