<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemActor extends Model
{
    use HasFactory;

    protected $primaryKey = 'system_actor_id';

    protected $fillable = [
        'system_actor_id',
        'actor_name',
        'actor_type',
        'description',
        'status',
        'soil_id',
        'user_id',
        'map_id',
        'admin_id',
        'role',
        'action',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['system_actor_id'] ?? $this->attributes['id'] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function soil()
    {
        return $this->belongsTo(
            Soil::class,
            'soil_id',
            'soil_id'
        );
    }
}
