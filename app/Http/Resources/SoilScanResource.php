<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoilScanResource extends JsonResource
{
    /**
     * Transform the resource into an array for mobile and API clients.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $scanDate = $this->scan_date 
            ? Carbon::parse($this->scan_date) 
            : ($this->created_at ? Carbon::parse($this->created_at) : now());

        if ($scanDate->isToday()) {
            $dateGroup = 'Today';
        } elseif ($scanDate->isYesterday()) {
            $dateGroup = 'Yesterday';
        } else {
            $dateGroup = $scanDate->format('F j, Y');
        }

        $imgUrl = $this->scan_image_url;

        return [
            'soil_scan_id'   => $this->soil_scan_id,
            'soil_id'        => $this->soil_id,
            'user_id'        => $this->user_id,
            'location_id'    => $this->location_id,
            'scan_date'      => $this->scan_date,
            'scan_time'      => $scanDate ? $scanDate->format('g:i A') : '',
            'date_group'     => $dateGroup,
            'scan_image'     => $this->scan_image,
            'scan_image_url' => $imgUrl,
            'scan_note'      => $this->scan_note,
            'platform'       => $this->platform ?? 'mobile',
            'color_name'     => $this->soil?->effective_soil_name ?? $this->soil?->soil_name ?? 'Determined Soil',
            'full_code'      => $this->soil?->full_code ?? '10YR 4/1',
            'hue'            => $this->soil?->hue ?? '10YR',
            'hex_color'      => $this->soil?->effective_hex_color ?? $this->soil?->munsellColor?->hex_color ?? '#8F694E',
            'description'    => $this->scan_note ?? $this->soil?->effective_description ?? 'Field scan record',
            'soil'           => $this->whenLoaded('soil'),
            'location'       => $this->whenLoaded('location'),
            'user'           => $this->whenLoaded('user'),
        ];
    }
}
