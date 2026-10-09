<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationActor extends Model
{
    protected $table = 'application_actors';
    protected $primaryKey = 'application_actor_id';

    protected $fillable = [
        'application_actor_id',
        'admin_id',
        'application_id',
        'role',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['application_actor_id'] ?? $this->attributes['id'] ?? null;
    }

    public function application()
    {
        return $this->belongsTo(
            Application::class,
            'application_id',
            'application_id'
        );
    }
}
