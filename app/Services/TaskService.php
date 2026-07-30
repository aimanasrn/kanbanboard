<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * Reorders a task and shifts positions of tasks within source and target columns.
     */
    public function reorderTask(int $taskId, string $newStatus, int $newPosition, ?int $userId = null): Task
    {
        return DB::transaction(function () use ($taskId, $newStatus, $newPosition, $userId) {
            $task = Task::findOrFail($taskId);
            $oldStatus = $task->status;
            $oldPosition = $task->position;

            if ($oldStatus === $newStatus) {
                // Moving within same column
                if ($oldPosition < $newPosition) {
                    Task::where('project_id', $task->project_id)
                        ->where('status', $newStatus)
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                } elseif ($oldPosition > $newPosition) {
                    Task::where('project_id', $task->project_id)
                        ->where('status', $newStatus)
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                }

                $task->update(['position' => $newPosition]);
            } else {
                // Moving to a different column
                // Shift down higher positions in old column
                Task::where('project_id', $task->project_id)
                    ->where('status', $oldStatus)
                    ->where('position', '>', $oldPosition)
                    ->decrement('position');

                // Shift up positions in target column
                Task::where('project_id', $task->project_id)
                    ->where('status', $newStatus)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');

                $updateData = [
                    'status' => $newStatus,
                    'position' => $newPosition,
                ];

                if ($newStatus === 'done') {
                    $updateData['completed_at'] = now();
                    // Stop timer if running
                    if ($task->timer_started_at) {
                        $seconds = now()->diffInSeconds($task->timer_started_at);
                        $addedHours = round($seconds / 3600, 2);
                        $updateData['actual_hours'] = ($task->actual_hours ?? 0) + $addedHours;
                        $updateData['timer_started_at'] = null;
                    }
                } else {
                    $updateData['completed_at'] = null;
                }

                $task->update($updateData);

                // Handle Recurring Task creation if task completed
                if ($newStatus === 'done' && $task->recurring_frequency && $oldStatus !== 'done') {
                    $this->createNextRecurringTask($task, $userId);
                }

                // Log activity
                $statusLabels = [
                    'backlog' => 'Backlog',
                    'todo' => 'To Do',
                    'in_progress' => 'In Progress',
                    'done' => 'Done',
                ];

                $fromName = $statusLabels[$oldStatus] ?? $oldStatus;
                $toName = $statusLabels[$newStatus] ?? $newStatus;

                ActivityLog::create([
                    'project_id' => $task->project_id,
                    'user_id' => $userId ?? $task->created_by,
                    'task_id' => $task->id,
                    'action' => 'moved',
                    'description' => "Moved task \"{$task->title}\" from {$fromName} to {$toName}",
                ]);
            }

            return $task->fresh();
        });
    }

    /**
     * Duplicates a task along with its labels and checklist items.
     */
    public function duplicateTask(Task $task, ?int $userId = null): Task
    {
        return DB::transaction(function () use ($task, $userId) {
            $maxPos = Task::where('project_id', $task->project_id)
                ->where('status', $task->status)
                ->max('position') ?? 0;

            $newTask = $task->replicate(['created_at', 'updated_at']);
            $newTask->title = "{$task->title} (Copy)";
            $newTask->position = $maxPos + 1;
            $newTask->created_by = $userId ?? $task->created_by;
            $newTask->save();

            foreach ($task->labels as $label) {
                $newTask->labels()->create([
                    'label' => $label->label,
                    'color' => $label->color,
                ]);
            }

            foreach ($task->checklists as $checklist) {
                $newTask->checklists()->create([
                    'title' => $checklist->title,
                    'completed' => false,
                    'position' => $checklist->position,
                ]);
            }

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => $userId ?? $task->created_by,
                'task_id' => $newTask->id,
                'action' => 'created',
                'description' => "Duplicated task \"{$task->title}\" as \"{$newTask->title}\"",
            ]);

            return $newTask;
        });
    }

    /**
     * Toggles live working timer on a task.
     */
    public function toggleTimer(Task $task, ?int $userId = null): Task
    {
        return DB::transaction(function () use ($task, $userId) {
            if ($task->timer_started_at) {
                // Stop timer & accumulate actual_hours
                $seconds = now()->diffInSeconds($task->timer_started_at);
                $addedHours = round($seconds / 3600, 2);
                $newActualHours = ($task->actual_hours ?? 0) + $addedHours;

                $task->update([
                    'timer_started_at' => null,
                    'actual_hours' => $newActualHours,
                ]);

                ActivityLog::create([
                    'project_id' => $task->project_id,
                    'user_id' => $userId ?? auth()->id() ?? $task->created_by,
                    'task_id' => $task->id,
                    'action' => 'updated',
                    'description' => "Stopped timer on \"{$task->title}\" (Added {$addedHours} hrs)",
                ]);
            } else {
                // Start timer
                $task->update([
                    'timer_started_at' => now(),
                ]);

                ActivityLog::create([
                    'project_id' => $task->project_id,
                    'user_id' => $userId ?? auth()->id() ?? $task->created_by,
                    'task_id' => $task->id,
                    'action' => 'updated',
                    'description' => "Started working timer on \"{$task->title}\"",
                ]);
            }

            return $task->fresh();
        });
    }

    /**
     * Creates next recurring task instance based on frequency.
     */
    protected function createNextRecurringTask(Task $task, ?int $userId = null): Task
    {
        $nextDueDate = match ($task->recurring_frequency) {
            'daily'   => now()->addDay(),
            'weekly'  => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default   => now()->addDay(),
        };

        $maxPos = Task::where('project_id', $task->project_id)
            ->where('status', 'todo')
            ->max('position') ?? 0;

        $nextTask = Task::create([
            'project_id'          => $task->project_id,
            'title'               => $task->title,
            'description'         => $task->description,
            'status'              => 'todo',
            'position'            => $maxPos + 1,
            'priority'            => $task->priority,
            'due_date'            => $nextDueDate,
            'assigned_to'         => $task->assigned_to,
            'created_by'          => $userId ?? $task->created_by,
            'estimated_hours'     => $task->estimated_hours,
            'actual_hours'        => null,
            'recurring_frequency' => $task->recurring_frequency,
        ]);

        foreach ($task->labels as $label) {
            $nextTask->labels()->create([
                'label' => $label->label,
                'color' => $label->color,
            ]);
        }

        foreach ($task->checklists as $checklist) {
            $nextTask->checklists()->create([
                'title'     => $checklist->title,
                'completed' => false,
                'position'  => $checklist->position,
            ]);
        }

        ActivityLog::create([
            'project_id'  => $task->project_id,
            'user_id'     => $userId ?? $task->created_by,
            'task_id'     => $nextTask->id,
            'action'      => 'created',
            'description' => "Auto-scheduled next " . ucfirst($task->recurring_frequency) . " recurring task for \"{$task->title}\"",
        ]);

        return $nextTask;
    }

    /**
     * Calculates project statistics.
     */
    public function calculateProjectStats(Project $project): array
    {
        $tasks = $project->tasks()->where('is_archived', false)->get();
        $total = $tasks->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'urgent' => 0,
                'overdue' => 0,
                'completion_rate' => 0,
                'total_estimated_hours' => 0,
                'total_actual_hours' => 0,
            ];
        }

        $completed = $tasks->where('status', 'done')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $urgent = $tasks->where('priority', 'urgent')->count();
        $overdue = $tasks->filter(fn($t) => $t->isOverdue())->count();
        $completionRate = round(($completed / $total) * 100);
        $totalEstimated = $tasks->sum('estimated_hours');
        $totalActual = $tasks->sum('actual_hours');

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'urgent' => $urgent,
            'overdue' => $overdue,
            'completion_rate' => $completionRate,
            'total_estimated_hours' => round($totalEstimated, 1),
            'total_actual_hours' => round($totalActual, 1),
        ];
    }
}
