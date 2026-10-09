<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array for mobile and API clients.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id'                => $this->user_id,
            'name'                   => $this->name,
            'email'                  => $this->email,
            'role'                   => $this->role,
            'student_id'             => $this->student_id,
            'section'                => $this->section,
            'student_record_status'  => $this->student_record_status,
            'status'                 => $this->status,
            'profile_photo_url'      => $this->profile_photo_url ?? null,
            'created_at'             => $this->created_at?->toIso8601String(),
        ];
    }
}
