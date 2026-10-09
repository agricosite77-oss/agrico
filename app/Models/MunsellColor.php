<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunsellColor extends Model
{
    protected $table = 'munsell_colors';
    protected $primaryKey = 'munsell_color_id';

    protected $fillable = [
        'munsell_color_id',
        'hue',
        'value',
        'chroma',
        'full_code',
        'soil_organic_index',
        'soil_type',
        'image',
        'image_path',
        'color_name',
        'hex_code',
        'description',
        'status',
        'admin_id',
        'last_admin_id',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['munsell_color_id'] ?? $this->attributes['id'] ?? null;
    }

    public function getImageAttribute($value)
    {
        return $value ?: ($this->attributes['image_path'] ?? null);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->hue) && $model->value !== null && $model->chroma !== null) {
                $model->full_code = trim($model->hue . ' ' . $model->value . '/' . $model->chroma);
            }

            if (!empty($model->full_code)) {
                $lookup = \App\Services\MunsellLookupService::lookup($model->full_code);
                if ($lookup) {
                    if (empty($model->soil_organic_index)) {
                        $model->soil_organic_index = $lookup['soil_organic_index'];
                    }
                    if (empty($model->soil_type)) {
                        $model->soil_type = $lookup['soil_type'];
                    }
                }
            }
        });
    }

    public function getSoilOrganicIndexAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return \App\Services\MunsellLookupService::getSoilOrganicIndex($this->full_code ?? '') ?? '—';
    }

    public function getSoilTypeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return \App\Services\MunsellLookupService::getSoilType($this->full_code ?? '') ?? '—';
    }

    public function getColorNameAttribute($value)
    {
        return $value ? ucwords(strtolower($value)) : null;
    }

    public function setColorNameAttribute($value)
    {
        $this->attributes['color_name'] = $value ? ucwords(strtolower($value)) : null;
    }

    public function soils()
    {
        return $this->hasMany(
            Soil::class,
            'color_id',
            'munsell_color_id'
        );
    }
}
