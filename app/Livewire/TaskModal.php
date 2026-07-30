<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskChecklist;
use App\Models\TaskLabel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public ?int $taskId = null;
    public int $projectId = 1;

    #[Rule('required|min:3|max:255')]
    public string $title = '';

    public string $description = '';
    public string $status = 'backlog';
    public string $priority = 'medium';
    public ?string $due_date = null;
    public ?int $assigned_to = null;
    public ?float $estimated_hours = null;
    public ?float $actual_hours = null;
    public ?string $recurring_frequency = null;

    // Checklists transient & persisted state
    public array $checklists = [];
    public string $newChecklistTitle = '';

    // Labels
    public array $labels = [];
    public string $newLabelText = '';
    public string $newLabelColor = 'purple';

    // File Attachments
    public $newAttachments = [];

    // Active Modal Tab: 'details', 'checklists', 'comments', 'attachments', 'activity'
    public string $activeTab = 'details';

    public function mount(?int $taskId = null, string $defaultStatus = 'backlog', int $projectId = 1): void
    {
        $this->projectId = $projectId;
        $this->taskId = $taskId;
        $this->status = $defaultStatus;

        if ($taskId) {
            $task = Task::with(['labels', 'checklists', 'comments', 'attachments', 'assignee'])->findOrFail($taskId);
            $this->title = $task->title;
            $this->description = $task->description ?? '';
            $this->status = $task->status;
            $this->priority = $task->priority;
            $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : null;
            $this->assigned_to = $task->assigned_to;
            $this->estimated_hours = $task->estimated_hours;
            $this->actual_hours = $task->actual_hours;
            $this->recurring_frequency = $task->recurring_frequency;
            $this->labels = $task->labels->toArray();
            $this->checklists = $task->checklists->toArray();
        } else {
            $this->resetForm();
            $this->status = $defaultStatus;
        }

        $this->isOpen = true;
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->due_date = null;
        $this->assigned_to = null;
        $this->estimated_hours = null;
        $this->actual_hours = null;
        $this->recurring_frequency = null;
        $this->checklists = [];
        $this->labels = [];
        $this->newAttachments = [];
        $this->activeTab = 'details';
    }

    public function addChecklistItem(): void
    {
        if (trim($this->newChecklistTitle) === '') {
            return;
        }

        $currentUser = auth()->user();

        if ($this->taskId) {
            $maxPos = TaskChecklist::where('task_id', $this->taskId)->max('position') ?? 0;
            $checklist = TaskChecklist::create([
                'task_id' => $this->taskId,
                'title' => trim($this->newChecklistTitle),
                'completed' => false,
                'position' => $maxPos + 1,
            ]);
            $this->checklists[] = $checklist->toArray();

            ActivityLog::create([
                'project_id' => $this->projectId,
                'user_id' => $currentUser->id,
                'task_id' => $this->taskId,
                'action' => 'updated',
                'description' => "Added subtask \"{$checklist->title}\"",
            ]);
        } else {
            $this->checklists[] = [
                'id' => null,
                'title' => trim($this->newChecklistTitle),
                'completed' => false,
            ];
        }

        $this->newChecklistTitle = '';
    }

    public function toggleChecklist(int $index): void
    {
        if (isset($this->checklists[$index])) {
            $this->checklists[$index]['completed'] = !$this->checklists[$index]['completed'];
            $currentUser = auth()->user();

            if (!empty($this->checklists[$index]['id'])) {
                TaskChecklist::where('id', $this->checklists[$index]['id'])
                    ->update(['completed' => $this->checklists[$index]['completed']]);

                $statusStr = $this->checklists[$index]['completed'] ? 'completed' : 'uncompleted';
                ActivityLog::create([
                    'project_id' => $this->projectId,
                    'user_id' => $currentUser->id,
                    'task_id' => $this->taskId,
                    'action' => 'updated',
                    'description' => "Marked subtask \"{$this->checklists[$index]['title']}\" as {$statusStr}",
                ]);
            }
        }
    }

    public function removeChecklist(int $index): void
    {
        if (isset($this->checklists[$index])) {
            if (!empty($this->checklists[$index]['id'])) {
                TaskChecklist::destroy($this->checklists[$index]['id']);
            }
            array_splice($this->checklists, $index, 1);
        }
    }

    public function addLabel(): void
    {
        if (trim($this->newLabelText) === '') {
            return;
        }

        if ($this->taskId) {
            $label = TaskLabel::create([
                'task_id' => $this->taskId,
                'label' => trim($this->newLabelText),
                'color' => $this->newLabelColor,
            ]);
            $this->labels[] = $label->toArray();
        } else {
            $this->labels[] = [
                'id' => null,
                'label' => trim($this->newLabelText),
                'color' => $this->newLabelColor,
            ];
        }

        $this->newLabelText = '';
    }

    public function removeLabel(int $index): void
    {
        if (isset($this->labels[$index])) {
            if (!empty($this->labels[$index]['id'])) {
                TaskLabel::destroy($this->labels[$index]['id']);
            }
            array_splice($this->labels, $index, 1);
        }
    }

    public function uploadAttachments(): void
    {
        $this->validate([
            'newAttachments.*' => 'file|max:10240', // max 10MB
        ]);

        if (!$this->taskId) {
            return;
        }

        $currentUser = auth()->user();

        foreach ($this->newAttachments as $file) {
            $path = $file->store('attachments', 'public');
            TaskAttachment::create([
                'task_id' => $this->taskId,
                'user_id' => $currentUser->id,
                'filename' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            ActivityLog::create([
                'project_id' => $this->projectId,
                'user_id' => $currentUser->id,
                'task_id' => $this->taskId,
                'action' => 'updated',
                'description' => "Uploaded attachment \"{$file->getClientOriginalName()}\"",
            ]);
        }

        $this->newAttachments = [];
        $this->dispatch('refresh-board');
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = TaskAttachment::find($attachmentId);
        if ($attachment) {
            Storage::disk('public')->delete($attachment->filename);
            $attachment->delete();
            $this->dispatch('refresh-board');
        }
    }

    public function save(): void
    {
        $this->validate();

        $currentUser = auth()->user();

        if ($this->taskId) {
            $task = Task::findOrFail($this->taskId);
            $oldStatus = $task->status;

            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'priority' => $this->priority,
                'due_date' => $this->due_date ? $this->due_date : null,
                'assigned_to' => $this->assigned_to,
                'estimated_hours' => $this->estimated_hours ? (float) $this->estimated_hours : null,
                'actual_hours' => $this->actual_hours ? (float) $this->actual_hours : null,
                'recurring_frequency' => $this->recurring_frequency ? $this->recurring_frequency : null,
                'completed_at' => ($this->status === 'done' && !$task->completed_at) ? now() : ($this->status !== 'done' ? null : $task->completed_at),
            ]);

            $desc = "Updated task details for \"{$task->title}\"";
            if ($oldStatus !== $this->status) {
                $desc = "Moved task \"{$task->title}\" from {$oldStatus} to {$this->status}";
            }

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => $currentUser->id,
                'task_id' => $task->id,
                'action' => 'updated',
                'description' => $desc,
            ]);
        } else {
            $maxPos = Task::where('project_id', $this->projectId)
                ->where('status', $this->status)
                ->max('position') ?? 0;

            $task = Task::create([
                'project_id' => $this->projectId,
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'position' => $maxPos + 1,
                'priority' => $this->priority,
                'due_date' => $this->due_date ? $this->due_date : null,
                'assigned_to' => $this->assigned_to,
                'estimated_hours' => $this->estimated_hours ? (float) $this->estimated_hours : null,
                'actual_hours' => $this->actual_hours ? (float) $this->actual_hours : null,
                'recurring_frequency' => $this->recurring_frequency ? $this->recurring_frequency : null,
                'created_by' => $currentUser->id,
                'completed_at' => $this->status === 'done' ? now() : null,
            ]);

            // Save labels
            foreach ($this->labels as $lbl) {
                $task->labels()->create([
                    'label' => $lbl['label'],
                    'color' => $lbl['color'] ?? 'purple',
                ]);
            }

            // Save checklists
            foreach ($this->checklists as $idx => $chk) {
                $task->checklists()->create([
                    'title' => $chk['title'],
                    'completed' => $chk['completed'] ?? false,
                    'position' => $idx,
                ]);
            }

            ActivityLog::create([
                'project_id' => $task->project_id,
                'user_id' => $currentUser->id,
                'task_id' => $task->id,
                'action' => 'created',
                'description' => "Created new task \"{$task->title}\"",
            ]);
        }

        $this->dispatch('task-saved', message: $this->taskId ? 'Task updated successfully' : 'Task created successfully');
    }

    public function render()
    {
        $users = User::all();
        $task = $this->taskId ? Task::with(['attachments', 'comments.user'])->find($this->taskId) : null;
        $taskActivities = $this->taskId 
            ? ActivityLog::where('task_id', $this->taskId)->with('user')->latest()->get()
            : collect();

        return view('livewire.task-modal', [
            'users' => $users,
            'task' => $task,
            'taskActivities' => $taskActivities,
        ]);
    }
}
