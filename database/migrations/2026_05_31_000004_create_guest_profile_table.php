<?php

declare(strict_types=1);

use App\Models\GuestProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guest_profile')) {
            Schema::create('guest_profile', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('name')->nullable();
                $table->string('eyebrow')->nullable();
                $table->text('description')->nullable();
                $table->string('main_photo')->nullable();
                $table->json('stories')->nullable();
                $table->string('commitment_label')->nullable();
                $table->text('commitment_title')->nullable();
                $table->json('commitments')->nullable();
                $table->string('important_label')->nullable();
                $table->text('important_title')->nullable();
                $table->text('important_description')->nullable();
                $table->json('important_items')->nullable();
                $table->string('gallery_label')->nullable();
                $table->text('gallery_title')->nullable();
                $table->text('gallery_description')->nullable();
                $table->json('gallery_items')->nullable();
                $table->string('contact_label')->nullable();
                $table->text('contact_title')->nullable();
                $table->text('contact_description')->nullable();
                $table->string('contact_button_label')->nullable();
                $table->json('contact_items')->nullable();
                $table->text('platform_title')->nullable();
                $table->text('platform_description')->nullable();
                $table->json('platform_links')->nullable();
                $table->string('contact_faq_label')->nullable();
                $table->text('contact_faq_title')->nullable();
                $table->text('contact_faq_description')->nullable();
                $table->json('contact_faqs')->nullable();
                $table->text('feedback_title')->nullable();
                $table->string('feedback_yes_label')->nullable();
                $table->string('feedback_no_label')->nullable();
                $table->text('feedback_prompt')->nullable();
                $table->text('feedback_help_title')->nullable();
                $table->string('feedback_wa_label')->nullable();
                $table->string('location_label')->nullable();
                $table->text('location_title')->nullable();
                $table->text('location_description')->nullable();
                $table->text('location_embed_url')->nullable();
                $table->text('location_name')->nullable();
                $table->text('location_address')->nullable();
                $table->text('location_google_maps_url')->nullable();
                $table->text('location_waze_url')->nullable();
                $table->json('location_notes')->nullable();
                $table->json('masukkan')->nullable();
                $table->timestamps();
            });
        }

        if (DB::table('guest_profile')->count() === 0) {
            $defaults = GuestProfile::defaults();

            foreach (['stories', 'commitments', 'important_items', 'gallery_items', 'contact_items', 'platform_links', 'contact_faqs', 'location_notes', 'masukkan'] as $column) {
                $defaults[$column] = json_encode($defaults[$column], JSON_UNESCAPED_UNICODE);
            }

            DB::table('guest_profile')->insert(array_merge(
                ['id' => (string) Str::uuid()],
                $defaults,
                ['created_at' => now(), 'updated_at' => now()],
            ));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_profile');
    }
};
