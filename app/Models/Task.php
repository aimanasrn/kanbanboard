<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'position',
        'priority',
        'due_date',
        'assigned_to',
        'created_by',
        'completed_at',
        'is_archived',
        'estimated_hours',
        'actual_hours',
        'timer_started_at',
        'recurring_frequency',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'is_archived' => 'boolean',
            'position' => 'integer',
            'estimated_hours' => 'float',
            'actual_hours' => 'float',
            'timer_started_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(TaskLabel::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('position', 'asc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function isTimerRunning(): bool
    {
        return !is_null($this->timer_started_at);
    }

    public function getElapsedTimeSeconds(): int
    {
        if (!$this->timer_started_at) {
            return 0;
        }
        return (int) now()->diffInSeconds($this->timer_started_at);
    }

    public function getChecklistProgressAttribute(): array
    {
        $total = $this->checklists->count();
        if ($total === 0) {
            return ['completed' => 0, 'total' => 0, 'percentage' => 0];
        }
        $completed = $this->checklists->where('completed', true)->count();
        $percentage = round(($completed / $total) * 100);

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $percentage,
        ];
    }
}
