<?php

declare(strict_types=1);

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_settings') || Schema::hasColumn('system_settings', 'hero_image')) {
            return;
        }

        Schema::table('system_settings', function (Blueprint $table): void {
            $table->text('hero_image')->nullable()->after('hero_description');
        });

        DB::table('system_settings')
            ->where('key', SystemSetting::HOME_KEY)
            ->whereNull('hero_image')
            ->update([
                'hero_image' => SystemSetting::defaults()['hero_image'],
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('system_settings') || ! Schema::hasColumn('system_settings', 'hero_image')) {
            return;
        }

        Schema::table('system_settings', function (Blueprint $table): void {
            $table->dropColumn('hero_image');
        });
    }
};
