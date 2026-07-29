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
                } else {
                    $updateData['completed_at'] = null;
                }

                $task->update($updateData);

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
            ];
        }

        $completed = $tasks->where('status', 'done')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $urgent = $tasks->where('priority', 'urgent')->count();
        $overdue = $tasks->filter(fn($t) => $t->isOverdue())->count();
        $completionRate = round(($completed / $total) * 100);

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'urgent' => $urgent,
            'overdue' => $overdue,
            'completion_rate' => $completionRate,
        ];
    }
}
