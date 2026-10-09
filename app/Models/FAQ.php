<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';

    protected $fillable = [
        'faq_id',
        'question',
        'category',
        'answer',
        'status',
        'admin_id',
        'last_admin_id',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['faq_id'] ?? $this->attributes['id'] ?? null;
    }

    public function interactions()
    {
        return $this->hasMany(
            FAQInteraction::class,
            'faq_id',
            'faq_id'
        );
    }
}
