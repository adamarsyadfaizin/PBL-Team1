<?php

declare(strict_types=1);

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $homeColumns = [
        'hero_label',
        'hero_title',
        'hero_description',
        'how_label',
        'how_title',
        'how_description',
        'how_step_1_title',
        'how_step_1_description',
        'how_step_2_title',
        'how_step_2_description',
        'how_step_3_title',
        'how_step_3_description',
        'rooms_label',
        'rooms_title',
        'rooms_description',
        'gallery_label',
        'gallery_title',
        'gallery_description',
        'facilities_label',
        'facilities_title',
        'facilities_description',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->text('value');
                $table->text('deskripsi')->nullable();
                $table->timestampTz('updated_at')->useCurrent();

                foreach ($this->homeColumns as $column) {
                    $table->text($column)->nullable();
                }
            });
        } else {
            Schema::table('system_settings', function (Blueprint $table): void {
                foreach ($this->homeColumns as $column) {
                    if (! Schema::hasColumn('system_settings', $column)) {
                        $table->text($column)->nullable();
                    }
                }
            });
        }

        DB::table('system_settings')->updateOrInsert(
            ['key' => SystemSetting::HOME_KEY],
            array_merge([
                'value' => SystemSetting::HOME_KEY,
                'deskripsi' => 'Pengaturan konten landing page.',
                'updated_at' => now(),
            ], SystemSetting::defaults()),
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        DB::table('system_settings')->where('key', SystemSetting::HOME_KEY)->delete();

        Schema::table('system_settings', function (Blueprint $table): void {
            foreach ($this->homeColumns as $column) {
                if (Schema::hasColumn('system_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
