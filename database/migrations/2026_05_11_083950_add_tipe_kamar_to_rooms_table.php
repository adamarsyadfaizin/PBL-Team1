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
        if (! Schema::hasTable('rooms') || Schema::hasColumn('rooms', 'tipe_kamar')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table): void {
            $table->string('tipe_kamar')
                ->nullable()
                ->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('rooms') || ! Schema::hasColumn('rooms', 'tipe_kamar')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table): void {
            $table->dropColumn('tipe_kamar');
        });
    }
};
