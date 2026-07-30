<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'color',
        'wip_limits',
    ];

    protected function casts(): array
    {
        return [
            'wip_limits' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('position', 'asc');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function getWipLimit(string $columnKey): int
    {
        $defaultLimits = [
            'backlog' => 0,
            'todo' => 10,
            'in_progress' => 5,
            'review' => 3,
            'done' => 0,
        ];

        $limits = $this->wip_limits ?? [];
        return isset($limits[$columnKey]) ? (int) $limits[$columnKey] : ($defaultLimits[$columnKey] ?? 0);
    }

    public function isMember(int $userId): bool
    {
        if ($this->created_by === $userId) {
            return true;
        }
        return $this->members()->where('user_id', $userId)->exists();
    }
}
