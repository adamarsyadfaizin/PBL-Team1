<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            return;
        }

        Schema::create('bookings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('kode_booking')->unique();
            $table->uuid('user_id');
            $table->uuid('room_id');
            $table->string('tipe_sewa');
            $table->date('tanggal_check_in');
            $table->date('tanggal_check_out');
            $table->unsignedInteger('durasi');
            $table->decimal('harga_snapshot', 12, 2);
            $table->decimal('total_tagihan', 12, 2);
            $table->text('catatan_penyewa')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->string('status')->default('pending');
            $table->text('alasan_pembatalan')->nullable();
            $table->uuid('dikonfirmasi_oleh')->nullable();
            $table->timestamp('tanggal_konfirmasi')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->nullOnDelete();
            $table->index(['room_id', 'status', 'tanggal_check_in', 'tanggal_check_out']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
