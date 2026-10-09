<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQInteraction extends Model
{
    use HasFactory;

    protected $table = 'faq_interactions';
    protected $primaryKey = 'faq_interaction_id';

    protected $fillable = [
        'faq_interaction_id',
        'action_type',
        'user_id',
        'admin_id',
        'faq_id',
        'timestamp',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['faq_interaction_id'] ?? $this->attributes['id'] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function faq()
    {
        return $this->belongsTo(
            FAQ::class,
            'faq_id',
            'faq_id'
        );
    }
}
