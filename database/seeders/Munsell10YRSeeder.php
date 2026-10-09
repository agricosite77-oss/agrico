<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\MunsellColor;
use App\Models\User;
use App\Services\EncycolorpediaService;
use Illuminate\Database\Seeder;

class Munsell10YRSeeder extends Seeder
{
    /**
     * Run the database seeds for the official 10YR Munsell Soil Color Chart matrix using EncycolorpediaService.
     */
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first() ?: User::first();
        $adminId = $adminUser ? ($adminUser->user_id ?? $adminUser->id) : 'ADM-001';
        $adminIdFormatted = Admin::formatId($adminId);

        $encycolorpediaService = app(EncycolorpediaService::class);
        $calibratedMatrix = EncycolorpediaService::getCalibrated10YRMatrix();

        foreach ($calibratedMatrix as $notation => $item) {
            $hue    = $item['hue'];
            $value  = (string) $item['value'];
            $chroma = (string) $item['chroma'];
            $name   = $item['name'];
            $hex    = strtolower($item['hex']);

            MunsellColor::updateOrCreate(
                ['full_code' => $notation],
                [
                    'hue'          => $hue,
                    'value'        => $value,
                    'chroma'       => $chroma,
                    'value_chroma' => "{$value}/{$chroma}",
                    'munsell_code' => $notation,
                    'color_name'   => $name,
                    'hex_code'     => $hex,
                    'description'  => "{$notation} - {$name}",
                    'status'       => 'Active',
                    'admin_id'     => $adminIdFormatted,
                ]
            );
        }
    }
}
