<?php

use App\Models\FAQ;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Seeds the faqs table with all default questions from config/faq.php
     * so that the Admin Manage FAQ page and website are synchronized.
     */
    public function up(): void
    {
        if (!Schema::hasTable('faqs')) {
            return;
        }

        $categories = config('faq.categories', []);
        $items = config('faq.items', []);

        foreach ($items as $item) {
            $catKey = $item['category'] ?? 'getting-started';
            $categoryName = $categories[$catKey] ?? ucwords(str_replace('-', ' ', $catKey));

            FAQ::updateOrCreate(
                ['question' => $item['question']],
                [
                    'category' => $categoryName,
                    'answer'   => $item['answer'],
                    'status'   => 'Active',
                    'admin_id' => 'ADM-001',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep data intact
    }
};
