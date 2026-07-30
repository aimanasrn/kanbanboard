<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ProjectManager extends Component
{
    // Create/Edit form
    public bool $showProjectModal = false;
    public ?int $editingProjectId = null;

    #[Rule('required|min:2|max:255')]
    public string $projectName = '';

    public string $projectDescription = '';
    public string $projectColor = '#6E63D9';

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $projectToDeleteId = null;
    public string $projectToDeleteName = '';

    // Toast
    public array $toastMessage = [];

    // Color options for project
    public array $colorOptions = [
        '#6E63D9', '#3B82F6', '#10B981', '#F59E0B',
        '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4',
    ];

    public function openCreateModal(): void
    {
        $this->editingProjectId = null;
        $this->projectName = '';
        $this->projectDescription = '';
        $this->projectColor = '#6E63D9';
        $this->showProjectModal = true;
    }

    public function openEditModal(int $projectId): void
    {
        $project = Project::findOrFail($projectId);
        $this->editingProjectId = $projectId;
        $this->projectName = $project->name;
        $this->projectDescription = $project->description ?? '';
        $this->projectColor = $project->color ?? '#6E63D9';
        $this->showProjectModal = true;
    }

    public function saveProject(): void
    {
        $this->validate([
            'projectName' => 'required|min:2|max:255',
        ]);

        if ($this->editingProjectId) {
            $project = Project::findOrFail($this->editingProjectId);

            // Only owner can edit
            if ($project->created_by !== auth()->id()) {
                $this->showToast('You can only edit your own projects.', 'error');
                return;
            }

            $project->update([
                'name'        => $this->projectName,
                'description' => $this->projectDescription,
                'color'       => $this->projectColor,
            ]);

            $this->showToast("Project \"{$project->name}\" updated successfully!", 'success');
        } else {
            $project = Project::create([
                'name'        => $this->projectName,
                'description' => $this->projectDescription,
                'color'       => $this->projectColor,
                'created_by'  => auth()->id(),
            ]);

            $this->showToast("Project \"{$project->name}\" created!", 'success');
        }

        $this->showProjectModal = false;
        $this->reset(['projectName', 'projectDescription', 'projectColor', 'editingProjectId']);
    }

    public function confirmDeleteProject(int $projectId): void
    {
        $project = Project::find($projectId);
        if ($project) {
            $this->projectToDeleteId = $projectId;
            $this->projectToDeleteName = $project->name;
            $this->showDeleteModal = true;
        }
    }

    public function deleteProjectConfirmed(): void
    {
        if ($this->projectToDeleteId) {
            $project = Project::find($this->projectToDeleteId);

            if ($project) {
                // Only owner can delete
                if ($project->created_by !== auth()->id()) {
                    $this->showToast('You can only delete your own projects.', 'error');
                    $this->showDeleteModal = false;
                    return;
                }

                $name = $project->name;
                $project->delete();
                $this->showToast("Project \"{$name}\" deleted.", 'info');
            }
        }

        $this->showDeleteModal = false;
        $this->projectToDeleteId = null;
        $this->projectToDeleteName = '';
    }

    public function goToProject(int $projectId): void
    {
        $this->redirect(route('project.show', $projectId));
    }

    public function showToast(string $message, string $type = 'success'): void
    {
        $this->toastMessage = [
            'id'      => microtime(true),
            'message' => $message,
            'type'    => $type,
        ];
    }

    public function render()
    {
        $projects = Project::withCount('tasks')
            ->with(['owner', 'tasks' => function ($q) {
                $q->where('is_archived', false)->select('id', 'project_id', 'status', 'priority', 'due_date');
            }])
            ->latest()
            ->get()
            ->map(function (Project $project) {
                $tasks         = $project->tasks;
                $total         = $tasks->count();
                $done          = $tasks->where('status', 'done')->count();
                $overdue       = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'done')->count();
                $inProgress    = $tasks->where('status', 'in_progress')->count();
                $completionPct = $total > 0 ? round(($done / $total) * 100) : 0;

                $project->stats = [
                    'total'          => $total,
                    'done'           => $done,
                    'in_progress'    => $inProgress,
                    'overdue'        => $overdue,
                    'completion_pct' => $completionPct,
                ];

                $project->is_owner = $project->created_by === auth()->id();

                return $project;
            });

        return view('livewire.project-manager', [
            'projects' => $projects,
        ])->layout('layouts.app', ['title' => 'My Projects — KanbanFlow']);
    }
}
