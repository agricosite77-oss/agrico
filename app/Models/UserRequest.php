<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    protected $table = 'user_requests';
    protected $primaryKey = 'user_request_id';

    protected $fillable = [
        'user_request_id',
        'request_code',
        'user_name',
        'user_email',
        'type',
        'description',
        'status',
        'admin_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['user_request_id'] ?? $this->attributes['id'] ?? null;
    }
}
