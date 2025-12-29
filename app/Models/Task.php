<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'assigned_by',
        'assigned_to',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (empty($task->task_id)) {
                $latestId = self::max('id');
                $nextId = $latestId ? $latestId + 1 : 1;
                $task->task_id = 'TASK' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

