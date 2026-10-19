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

        static::saved(function ($model) {
            // Synchronize with soils table so admin changes (description, name, etc.) immediately reflect everywhere
            $soilUpdates = [];
            if ($model->description !== null && trim($model->description) !== '') {
                $soilUpdates['description'] = trim($model->description);
            }
            if ($model->color_name !== null && trim($model->color_name) !== '') {
                $soilUpdates['soil_name'] = trim($model->color_name);
            }
            if (!empty($model->munsell_color_id)) {
                $soilUpdates['color_id'] = $model->munsell_color_id;
            }
            if (!empty($model->hue)) {
                $soilUpdates['hue'] = $model->hue;
            }

            if (!empty($soilUpdates)) {
                $query = Soil::query();
                $hasCond = false;
                if (!empty($model->munsell_color_id)) {
                    $query->where('color_id', $model->munsell_color_id);
                    $hasCond = true;
                }
                if (!empty($model->full_code)) {
                    $clean = trim($model->full_code);
                    if ($hasCond) {
                        $query->orWhere('full_code', $clean)
                              ->orWhereRaw('UPPER(TRIM(full_code)) = ?', [strtoupper($clean)]);
                    } else {
                        $query->where('full_code', $clean)
                              ->orWhereRaw('UPPER(TRIM(full_code)) = ?', [strtoupper($clean)]);
                        $hasCond = true;
                    }
                }
                if ($hasCond) {
                    $query->update($soilUpdates);
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
