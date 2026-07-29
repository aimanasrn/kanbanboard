<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
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

    #[On('refresh-board')]
    public function refreshBoard(): void
    {
        // Triggers re-render
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

        // Search Filter
        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
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

        // Group tasks by column status
        $tasksByColumn = [];
        foreach ($columns as $col) {
            $tasksByColumn[$col['key']] = $allTasks->where('status', $col['key'])->values();
        }

        return view('livewire.kanban-board', [
            'columns' => $columns,
            'tasksByColumn' => $tasksByColumn,
        ]);
    }
}
