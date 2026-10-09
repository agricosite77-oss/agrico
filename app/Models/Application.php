<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $primaryKey = 'application_id';

    protected $fillable = [
        'application_id',
        'application_name',
        'version',
        'file_path',
        'filename',
        'file_size',
        'release_notes',
        'status',
        'uploaded_by',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['application_id'] ?? $this->attributes['id'] ?? null;
    }

    public function downloads()
    {
        return $this->hasMany(
            Download::class,
            'application_id',
            'application_id'
        );
    }
}
