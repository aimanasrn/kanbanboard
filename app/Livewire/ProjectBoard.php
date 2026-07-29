<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLabel;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectBoard extends Component
{
    public int $projectId = 1;
    public string $viewMode = 'kanban'; // 'kanban' or 'timeline'
    public string $search = '';
    public string $priorityFilter = 'all';
    public string $assigneeFilter = 'all';
    public string $dueDateFilter = 'all';
    public string $labelFilter = 'all';

    // Professional Calendar State
    public int $calendarMonth;
    public int $calendarYear;
    public string $calendarSubView = 'month'; // 'month' or 'list'

    public bool $showTaskModal = false;
    public ?int $editingTaskId = null;
    public string $defaultColumnStatus = 'backlog';

    public bool $showActivityDrawer = false;
    public bool $showStatsModal = false;
    public bool $showAiStandupModal = false;
    public bool $showShortcutsModal = false;
    public bool $showProfileModal = false;

    public bool $showDeleteModal = false;
    public ?int $taskToDeleteId = null;

    public array $toastMessage = [];

    protected TaskService $taskService;

    public function boot(TaskService $taskService): void
    {
        $this->taskService = $taskService;
    }

    public function mount(?int $projectId = null): void
    {
        $project = $projectId ? Project::find($projectId) : Project::first();
        if ($project) {
            $this->projectId = $project->id;
        }

        $now = Carbon::now();
        $this->calendarMonth = $now->month;
        $this->calendarYear = $now->year;
    }

    #[On('open-profile-modal')]
    public function openProfileModal(): void
    {
        $this->showProfileModal = true;
    }

    #[On('close-profile-modal')]
    public function closeProfileModal(): void
    {
        $this->showProfileModal = false;
    }

    #[On('profile-updated')]
    public function handleProfileUpdated(string $message = 'Profile updated'): void
    {
        $this->showProfileModal = false;
        $this->showToast($message, 'success');
    }

    public function prevCalendarMonth(): void
    {
        $dt = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $dt->month;
        $this->calendarYear = $dt->year;
    }

    public function nextCalendarMonth(): void
    {
        $dt = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $dt->month;
        $this->calendarYear = $dt->year;
    }

    public function todayCalendarMonth(): void
    {
        $now = Carbon::now();
        $this->calendarMonth = $now->month;
        $this->calendarYear = $now->year;
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['kanban', 'timeline']) ? $mode : 'kanban';
    }

    #[On('toggle-shortcuts-modal')]
    public function toggleShortcutsModal(): void
    {
        $this->showShortcutsModal = !$this->showShortcutsModal;
    }

    public function generateAiStandupSummary(): array
    {
        $project = Project::with(['tasks.assignee', 'tasks.checklists'])->findOrFail($this->projectId);
        $tasks = $project->tasks;

        $completed = $tasks->where('status', 'done');
        $inProgress = $tasks->where('status', 'in_progress');
        $review = $tasks->where('status', 'review');
        $backlog = $tasks->where('status', 'backlog');
        $urgent = $tasks->where('priority', 'urgent')->where('status', '!=', 'done');

        return [
            'health_score' => $tasks->count() > 0 ? round(($completed->count() / $tasks->count()) * 100) : 100,
            'completed_tasks' => $completed->pluck('title')->take(4)->toArray(),
            'active_workload' => $inProgress->merge($review)->pluck('title')->take(4)->toArray(),
            'bottlenecks' => $urgent->map(function($t) {
                return "Task #{$t->id}: {$t->title} (" . ($t->assignee ? $t->assignee->name : 'Unassigned') . ")";
            })->toArray(),
            'recommendation' => "Focus on resolving " . $urgent->count() . " urgent blocker(s) in Review and In Progress before moving new backlog items."
        ];
    }

    public function exportTasksCsv(): StreamedResponse
    {
        $project = Project::findOrFail($this->projectId);
        $tasks = Task::where('project_id', $this->projectId)
            ->with(['assignee', 'labels', 'checklists'])
            ->orderBy('position', 'asc')
            ->get();

        $filename = "project_tasks_" . date('Y-m-d_H-i') . ".csv";

        return response()->streamDownload(function () use ($tasks) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Task ID', 'Title', 'Status', 'Priority', 'Assignee', 'Due Date', 'Checklist Progress', 'Labels']);

            foreach ($tasks as $t) {
                $progress = $t->checklist_progress;
                $chkStr = $progress['total'] > 0 ? "{$progress['completed']}/{$progress['total']}" : 'N/A';
                $lblStr = $t->labels->pluck('label')->implode(', ');

                fputcsv($handle, [
                    $t->id,
                    $t->title,
                    strtoupper($t->status),
                    strtoupper($t->priority),
                    $t->assignee ? $t->assignee->name : 'Unassigned',
                    $t->due_date ? $t->due_date->format('Y-m-d H:i') : 'None',
                    $chkStr,
                    $lblStr,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    #[On('open-task-modal')]
    public function openTaskModal(?int $taskId = null, string $status = 'backlog'): void
    {
        $this->editingTaskId = $taskId;
        $this->defaultColumnStatus = $status;
        $this->showTaskModal = true;
    }

    #[On('close-task-modal')]
    public function closeTaskModal(): void
    {
        $this->showTaskModal = false;
        $this->editingTaskId = null;
    }

    #[On('task-saved')]
    public function handleTaskSaved(string $message = 'Task saved successfully'): void
    {
        $this->showTaskModal = false;
        $this->editingTaskId = null;
        $this->dispatch('refresh-board');
        $this->showToast($message, 'success');
    }

    #[On('confirm-delete-task')]
    public function confirmDeleteTask(int $taskId): void
    {
        $this->taskToDeleteId = $taskId;
        $this->showDeleteModal = true;
    }

    public function deleteTaskConfirmed(): void
    {
        if ($this->taskToDeleteId) {
            $task = Task::find($this->taskToDeleteId);
            if ($task) {
                $title = $task->title;

                ActivityLog::create([
                    'project_id' => $task->project_id,
                    'user_id' => $task->assigned_to ?? $task->created_by,
                    'action' => 'deleted',
                    'description' => "Deleted task \"{$title}\"",
                ]);

                $task->delete();
                $this->showToast("Task \"{$title}\" deleted", 'info');
                $this->dispatch('refresh-board');
            }
        }
        $this->showDeleteModal = false;
        $this->taskToDeleteId = null;
    }

    #[On('duplicate-task')]
    public function duplicateTask(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $newTask = $this->taskService->duplicateTask($task);
        $this->dispatch('refresh-board');
        $this->showToast("Duplicated task as \"{$newTask->title}\"", 'success');
    }

    #[On('archive-task')]
    public function archiveTask(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $task->update(['is_archived' => !$task->is_archived]);
        $statusStr = $task->is_archived ? 'archived' : 'unarchived';
        $this->dispatch('refresh-board');
        $this->showToast("Task {$statusStr} successfully", 'info');
    }

    #[On('reorder-task')]
    public function updateTaskOrder(int $taskId, string $newStatus, int $newPosition): void
    {
        $this->taskService->reorderTask($taskId, $newStatus, $newPosition);
        $this->dispatch('refresh-board');
        $this->showToast("Updated task position", 'success');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->priorityFilter = 'all';
        $this->assigneeFilter = 'all';
        $this->dueDateFilter = 'all';
        $this->labelFilter = 'all';
    }

    public function showToast(string $message, string $type = 'success'): void
    {
        $this->toastMessage = [
            'id' => microtime(true),
            'message' => $message,
            'type' => $type,
        ];
    }

    public function render()
    {
        $project = Project::with(['tasks' => function ($query) {
            $query->where('is_archived', false)
                ->with(['assignee', 'labels', 'checklists', 'comments', 'attachments']);
        }])->findOrFail($this->projectId);

        $allUsers = User::all();
        $allLabels = TaskLabel::whereHas('task', function ($q) {
            $q->where('project_id', $this->projectId);
        })->pluck('label')->unique();

        $stats = $this->taskService->calculateProjectStats($project);
        $recentActivities = ActivityLog::where('project_id', $this->projectId)
            ->with(['user', 'task'])
            ->latest()
            ->take(15)
            ->get();

        $aiSummary = $this->showAiStandupModal ? $this->generateAiStandupSummary() : null;

        // Calculate Calendar Grid Days
        $currentMonthDate = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1);
        $startOfWeek = $currentMonthDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentMonthDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarDays = [];
        $day = $startOfWeek->copy();
        while ($day->lte($endOfWeek)) {
            $calendarDays[] = [
                'date' => $day->format('Y-m-d'),
                'day_number' => $day->day,
                'is_current_month' => $day->month === $this->calendarMonth,
                'is_today' => $day->isToday(),
            ];
            $day->addDay();
        }

        // Group Tasks by Date for Calendar Grid
        $tasksByDate = [];
        foreach ($project->tasks as $t) {
            if ($t->due_date) {
                $d = $t->due_date->format('Y-m-d');
                $tasksByDate[$d][] = $t;
            }
        }

        return view('livewire.project-board', [
            'project' => $project,
            'allUsers' => $allUsers,
            'allLabels' => $allLabels,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'aiSummary' => $aiSummary,
            'currentMonthDate' => $currentMonthDate,
            'calendarDays' => $calendarDays,
            'tasksByDate' => $tasksByDate,
        ]);
    }
}
