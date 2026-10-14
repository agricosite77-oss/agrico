<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'platform',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForWebsite($query)
    {
        return $query->where(function ($q) {
            $q->where('platform', 'website')
              ->orWhereIn('type', ['account_approved', 'account_disapproved']);
        });
    }

    public function scopeForMobile($query)
    {
        return $query->where(function ($q) {
            $q->where('platform', 'mobile')
              ->orWhereIn('type', ['scan_approved', 'scan_rejected']);
        });
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
