<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class KanbanBoard extends Component
{
    public int $projectId;
    public string $search = '';
    public string $priorityFilter = 'all';
    public string $assigneeFilter = 'all';
    public string $dueDateFilter = 'all';
    public string $labelFilter = 'all';

    // Bulk Actions Selection State
    public array $selectedTaskIds = [];
    public bool $showBulkDeleteModal = false;

    // WIP Limit Settings State
    public bool $showWipModal = false;
    public string $editingWipColumn = '';
    public int $editingWipLimit = 0;

    #[On('refresh-board')]
    public function refreshBoard(): void
    {
        // Triggers re-render
    }

    public function toggleTimer(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $isStarting = !$task->timer_started_at;
        $this->taskService->toggleTimer($task, auth()->id());
        $this->dispatch('refresh-board');
        $this->dispatch('show-toast', message: $isStarting ? "Started timer on \"{$task->title}\"" : "Stopped timer on \"{$task->title}\"", type: $isStarting ? 'info' : 'success');
    }

    public function openWipModal(string $columnKey): void
    {
        $project = Project::findOrFail($this->projectId);
        $this->editingWipColumn = $columnKey;
        $this->editingWipLimit = $project->getWipLimit($columnKey);
        $this->showWipModal = true;
    }

    public function saveWipLimit(): void
    {
        $project = Project::findOrFail($this->projectId);
        $wipLimits = $project->wip_limits ?? [];
        $wipLimits[$this->editingWipColumn] = max(0, (int) $this->editingWipLimit);

        $project->update(['wip_limits' => $wipLimits]);
        $this->showWipModal = false;
        $this->dispatch('show-toast', message: 'Column WIP limit updated!', type: 'success');
        $this->dispatch('refresh-board');
    }

    public function toggleSelectTask(int $taskId): void
    {
        if (in_array($taskId, $this->selectedTaskIds)) {
            $this->selectedTaskIds = array_diff($this->selectedTaskIds, [$taskId]);
        } else {
            $this->selectedTaskIds[] = $taskId;
        }
    }

    public function selectAllInColumn(string $columnKey): void
    {
        $columnTaskIds = Task::where('project_id', $this->projectId)
            ->where('status', $columnKey)
            ->where('is_archived', false)
            ->pluck('id')
            ->toArray();

        // Check if all are already selected
        $alreadySelected = array_intersect($columnTaskIds, $this->selectedTaskIds);
        if (count($alreadySelected) === count($columnTaskIds) && count($columnTaskIds) > 0) {
            // Deselect all in column
            $this->selectedTaskIds = array_diff($this->selectedTaskIds, $columnTaskIds);
        } else {
            // Add column task IDs to selection
            $this->selectedTaskIds = array_unique(array_merge($this->selectedTaskIds, $columnTaskIds));
        }
    }

    public function selectAll(): void
    {
        $allTaskIds = Task::where('project_id', $this->projectId)
            ->where('is_archived', false)
            ->pluck('id')
            ->toArray();

        if (count($this->selectedTaskIds) === count($allTaskIds)) {
            $this->selectedTaskIds = [];
        } else {
            $this->selectedTaskIds = $allTaskIds;
        }
    }

    public function clearSelection(): void
    {
        $this->selectedTaskIds = [];
    }

    public function bulkMoveStatus(string $newStatus): void
    {
        if (empty($this->selectedTaskIds)) {
            return;
        }

        $tasks = Task::whereIn('id', $this->selectedTaskIds)->get();
        $count = $tasks->count();
        $statusLabels = [
            'backlog' => 'Backlog',
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done',
        ];
        $targetName = $statusLabels[$newStatus] ?? $newStatus;

        foreach ($tasks as $task) {
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'done' && !$task->completed_at) {
                $updateData['completed_at'] = now();
            } elseif ($newStatus !== 'done') {
                $updateData['completed_at'] = null;
            }
            $task->update($updateData);

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => auth()->id() ?? $task->created_by,
                'task_id' => $task->id,
                'action' => 'moved',
                'description' => "Bulk moved task \"{$task->title}\" to {$targetName}",
            ]);
        }

        $this->clearSelection();
        $this->dispatch('show-toast', message: "Moved {$count} task(s) to {$targetName}", type: 'success');
        $this->dispatch('refresh-board');
    }

    public function bulkAssign(?int $userId = null): void
    {
        if (empty($this->selectedTaskIds)) {
            return;
        }

        $tasks = Task::whereIn('id', $this->selectedTaskIds)->get();
        $count = $tasks->count();
        $assigneeName = 'Unassigned';

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $assigneeName = $user->name;
            }
        }

        foreach ($tasks as $task) {
            $task->update(['assigned_to' => $userId]);

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => auth()->id() ?? $task->created_by,
                'task_id' => $task->id,
                'action' => 'updated',
                'description' => "Bulk assigned task \"{$task->title}\" to {$assigneeName}",
            ]);
        }

        $this->clearSelection();
        $this->dispatch('show-toast', message: "Assigned {$count} task(s) to {$assigneeName}", type: 'success');
        $this->dispatch('refresh-board');
    }

    public function bulkSetPriority(string $priority): void
    {
        if (empty($this->selectedTaskIds)) {
            return;
        }

        $tasks = Task::whereIn('id', $this->selectedTaskIds)->get();
        $count = $tasks->count();

        foreach ($tasks as $task) {
            $task->update(['priority' => $priority]);

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => auth()->id() ?? $task->created_by,
                'task_id' => $task->id,
                'action' => 'updated',
                'description' => "Bulk set priority of \"{$task->title}\" to " . ucfirst($priority),
            ]);
        }

        $this->clearSelection();
        $this->dispatch('show-toast', message: "Updated priority for {$count} task(s) to " . ucfirst($priority), type: 'success');
        $this->dispatch('refresh-board');
    }

    public function bulkArchive(): void
    {
        if (empty($this->selectedTaskIds)) {
            return;
        }

        $tasks = Task::whereIn('id', $this->selectedTaskIds)->get();
        $count = $tasks->count();

        foreach ($tasks as $task) {
            $task->update(['is_archived' => true]);

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => auth()->id() ?? $task->created_by,
                'task_id' => $task->id,
                'action' => 'updated',
                'description' => "Bulk archived task \"{$task->title}\"",
            ]);
        }

        $this->clearSelection();
        $this->dispatch('show-toast', message: "Archived {$count} task(s)", type: 'info');
        $this->dispatch('refresh-board');
    }

    public function confirmBulkDelete(): void
    {
        if (!empty($this->selectedTaskIds)) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDeleteConfirmed(): void
    {
        if (!empty($this->selectedTaskIds)) {
            $tasks = Task::whereIn('id', $this->selectedTaskIds)->get();
            $count = $tasks->count();

            foreach ($tasks as $task) {
                ActivityLog::create([
                    'project_id' => $task->project_id,
                    'user_id' => auth()->id() ?? $task->created_by,
                    'action' => 'deleted',
                    'description' => "Deleted task \"{$task->title}\" via bulk action",
                ]);
                $task->delete();
            }

            $this->clearSelection();
            $this->dispatch('show-toast', message: "Deleted {$count} task(s)", type: 'info');
            $this->dispatch('refresh-board');
        }

        $this->showBulkDeleteModal = false;
    }

    public function render()
    {
        $columns = [
            [
                'key' => 'backlog',
                'title' => 'Backlog',
                'color' => '#6E63D9',
                'badge' => 'bg-[#6E63D9]/10 text-[#6E63D9]',
                'dot' => 'bg-[#6E63D9]',
            ],
            [
                'key' => 'todo',
                'title' => 'To Do',
                'color' => '#3B82F6',
                'badge' => 'bg-blue-500/10 text-blue-600',
                'dot' => 'bg-blue-500',
            ],
            [
                'key' => 'in_progress',
                'title' => 'In Progress',
                'color' => '#FFC857',
                'badge' => 'bg-amber-500/10 text-amber-600',
                'dot' => 'bg-amber-500',
            ],
            [
                'key' => 'review',
                'title' => 'Review',
                'color' => '#A98BEF',
                'badge' => 'bg-purple-500/10 text-purple-600',
                'dot' => 'bg-purple-500',
            ],
            [
                'key' => 'done',
                'title' => 'Done',
                'color' => '#72D49A',
                'badge' => 'bg-emerald-500/10 text-emerald-600',
                'dot' => 'bg-emerald-500',
            ],
        ];

        $query = Task::where('project_id', $this->projectId)
            ->where('is_archived', false)
            ->with(['assignee', 'labels', 'checklists', 'comments', 'attachments']);

        // Search Filter (PostgreSQL & SQLite compatible)
        if (!empty($this->search)) {
            $term = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(title) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$term]);
            });
        }

        // Priority Filter
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        // Assignee Filter
        if ($this->assigneeFilter !== 'all') {
            if ($this->assigneeFilter === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', (int) $this->assigneeFilter);
            }
        }

        // Due Date Filter
        if ($this->dueDateFilter !== 'all') {
            if ($this->dueDateFilter === 'overdue') {
                $query->where('due_date', '<', now())->where('status', '!=', 'done');
            } elseif ($this->dueDateFilter === 'today') {
                $query->whereDate('due_date', now()->today());
            } elseif ($this->dueDateFilter === 'this_week') {
                $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
            }
        }

        // Label Filter
        if ($this->labelFilter !== 'all') {
            $query->whereHas('labels', function ($q) {
                $q->where('label', $this->labelFilter);
            });
        }

        $allTasks = $query->orderBy('position', 'asc')->get();
        $allUsers = User::all();
        $project = Project::find($this->projectId);

        // Group tasks by column status
        $tasksByColumn = [];
        foreach ($columns as $col) {
            $tasksByColumn[$col['key']] = $allTasks->where('status', $col['key'])->values();
        }

        return view('livewire.kanban-board', [
            'columns' => $columns,
            'tasksByColumn' => $tasksByColumn,
            'allUsers' => $allUsers,
            'project' => $project,
        ]);
    }
}
