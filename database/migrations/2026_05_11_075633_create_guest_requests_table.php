<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guest_requests', function (Blueprint $table) {

            $table->id();

            $table->string('nama');
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('request_type');

            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();

            $table->string('tipe_kamar')->nullable();
            $table->string('tipe_sewa')->nullable();
            $table->string('jumlah_tamu')->nullable();

            $table->string('complaint_category')->nullable();

            $table->text('pesan')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_requests');
    }
};
