<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table): void {
            if (! Schema::hasColumn('rooms', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('foto_utama');
            }

            if (! Schema::hasColumn('rooms', 'video_path')) {
                $table->string('video_path')->nullable()->after('gallery_images');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table): void {
            if (Schema::hasColumn('rooms', 'video_path')) {
                $table->dropColumn('video_path');
            }

            if (Schema::hasColumn('rooms', 'gallery_images')) {
                $table->dropColumn('gallery_images');
            }
        });
    }
};
