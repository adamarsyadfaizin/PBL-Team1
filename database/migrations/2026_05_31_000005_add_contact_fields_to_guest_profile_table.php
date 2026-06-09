<?php

declare(strict_types=1);

use App\Models\GuestProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guest_profile')) {
            return;
        }

        Schema::table('guest_profile', function (Blueprint $table): void {
            if (! Schema::hasColumn('guest_profile', 'contact_label')) {
                $table->string('contact_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_title')) {
                $table->text('contact_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_description')) {
                $table->text('contact_description')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_button_label')) {
                $table->string('contact_button_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_items')) {
                $table->json('contact_items')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'platform_title')) {
                $table->text('platform_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'platform_description')) {
                $table->text('platform_description')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'platform_links')) {
                $table->json('platform_links')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_faq_label')) {
                $table->string('contact_faq_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_faq_title')) {
                $table->text('contact_faq_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_faq_description')) {
                $table->text('contact_faq_description')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'contact_faqs')) {
                $table->json('contact_faqs')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_title')) {
                $table->text('feedback_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_yes_label')) {
                $table->string('feedback_yes_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_no_label')) {
                $table->string('feedback_no_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_prompt')) {
                $table->text('feedback_prompt')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_help_title')) {
                $table->text('feedback_help_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'feedback_wa_label')) {
                $table->string('feedback_wa_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_label')) {
                $table->string('location_label')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_title')) {
                $table->text('location_title')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_description')) {
                $table->text('location_description')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_embed_url')) {
                $table->text('location_embed_url')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_name')) {
                $table->text('location_name')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_address')) {
                $table->text('location_address')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_google_maps_url')) {
                $table->text('location_google_maps_url')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_waze_url')) {
                $table->text('location_waze_url')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'location_notes')) {
                $table->json('location_notes')->nullable();
            }

            if (! Schema::hasColumn('guest_profile', 'masukkan')) {
                $table->json('masukkan')->nullable();
            }
        });

        $defaults = GuestProfile::defaults();
        $jsonColumns = ['contact_items', 'platform_links', 'contact_faqs', 'location_notes', 'masukkan'];
        $columns = [
            'contact_label',
            'contact_title',
            'contact_description',
            'contact_button_label',
            'platform_title',
            'platform_description',
            'contact_faq_label',
            'contact_faq_title',
            'contact_faq_description',
            'feedback_title',
            'feedback_yes_label',
            'feedback_no_label',
            'feedback_prompt',
            'feedback_help_title',
            'feedback_wa_label',
            'location_label',
            'location_title',
            'location_description',
            'location_embed_url',
            'location_name',
            'location_address',
            'location_google_maps_url',
            'location_waze_url',
            ...$jsonColumns,
        ];

        $payload = [];

        foreach ($columns as $column) {
            $payload[$column] = in_array($column, $jsonColumns, true)
                ? json_encode($defaults[$column], JSON_UNESCAPED_UNICODE)
                : $defaults[$column];
        }

        DB::table('guest_profile')->whereNull('contact_title')->update($payload);
    }

    public function down(): void
    {
        if (! Schema::hasTable('guest_profile')) {
            return;
        }

        Schema::table('guest_profile', function (Blueprint $table): void {
            foreach ([
                'contact_label',
                'contact_title',
                'contact_description',
                'contact_button_label',
                'contact_items',
                'platform_title',
                'platform_description',
                'platform_links',
                'contact_faq_label',
                'contact_faq_title',
                'contact_faq_description',
                'contact_faqs',
                'feedback_title',
                'feedback_yes_label',
                'feedback_no_label',
                'feedback_prompt',
                'feedback_help_title',
                'feedback_wa_label',
                'location_label',
                'location_title',
                'location_description',
                'location_embed_url',
                'location_name',
                'location_address',
                'location_google_maps_url',
                'location_waze_url',
                'location_notes',
                'masukkan',
            ] as $column) {
                if (Schema::hasColumn('guest_profile', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
