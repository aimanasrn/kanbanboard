<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Livewire\Component;

class CommentSection extends Component
{
    public int $taskId;
    public string $commentText = '';

    public function addComment(): void
    {
        if (trim($this->commentText) === '') {
            return;
        }

        $currentUser = User::first(); // Active demo user
        $task = Task::findOrFail($this->taskId);

        $comment = TaskComment::create([
            'task_id' => $this->taskId,
            'user_id' => $currentUser->id,
            'comment' => trim($this->commentText),
        ]);

        ActivityLog::create([
            'project_id' => $task->project_id,
            'user_id' => $currentUser->id,
            'task_id' => $task->id,
            'action' => 'commented',
            'description' => "Commented on task \"{$task->title}\"",
        ]);

        $this->commentText = '';
        $this->dispatch('refresh-board');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = TaskComment::find($commentId);
        if ($comment) {
            $comment->delete();
            $this->dispatch('refresh-board');
        }
    }

    public function render()
    {
        $comments = TaskComment::where('task_id', $this->taskId)
            ->with('user')
            ->latest()
            ->get();

        $activeUser = User::first();

        return view('livewire.comment-section', [
            'comments' => $comments,
            'activeUser' => $activeUser,
        ]);
    }
}
