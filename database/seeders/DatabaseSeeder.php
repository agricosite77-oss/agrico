<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Essential system seeders (System Admins & Official 207 Munsell Chart Standards)
        $this->call([
            AdminSeeder::class,
            Munsell10YRSeeder::class,
            Munsell10RSeeder::class,
            Munsell7_5YRSeeder::class,
            Munsell5YRSeeder::class,
            Munsell2_5YRSeeder::class,
            Munsell2_5YSeeder::class,
            FullMunsellDescriptionsSeeder::class,
            FaqSeeder::class,
            AdminRecordSeeder::class,
        ]);
    }
}
