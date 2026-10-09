<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $primaryKey = 'download_id';

    protected $fillable = [
        'download_id',
        'user_id',
        'application_id',
        'download_type',
        'download_date',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['download_id'] ?? $this->attributes['id'] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
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
