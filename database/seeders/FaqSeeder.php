<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Seed the application's FAQ database table from config/faq.php.
     */
    public function run(): void
    {
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
}
