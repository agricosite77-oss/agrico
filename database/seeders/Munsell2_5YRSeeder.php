<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\MunsellColor;
use App\Models\User;
use App\Services\EncycolorpediaService;
use Illuminate\Database\Seeder;

class Munsell2_5YRSeeder extends Seeder
{
    /**
     * Run the database seeds for the official 2.5YR Munsell Soil Color Chart matrix using EncycolorpediaService.
     */
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first() ?: User::first();
        $adminId = $adminUser ? ($adminUser->user_id ?? $adminUser->id) : 'ADM-001';
        $adminIdFormatted = Admin::formatId($adminId);

        $encycolorpediaService = app(EncycolorpediaService::class);
        $calibratedMatrix = EncycolorpediaService::getCalibrated2_5YRMatrix();

        foreach ($calibratedMatrix as $notation => $item) {
            $fetchedData = $encycolorpediaService->fetchMunsellData($notation);

            $hue    = $item['hue'];
            $value  = (string) $item['value'];
            $chroma = (string) $item['chroma'];
            $name   = $item['name'] ?: $fetchedData['color_name'];
            $hex    = strtolower($item['hex'] ?: $fetchedData['hex_code']);

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
