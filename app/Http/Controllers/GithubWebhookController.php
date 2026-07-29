<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $request->header('X-GitHub-Event', 'pull_request');

        $affectedTasks = [];

        if ($event === 'pull_request') {
            $action = $payload['action'] ?? '';
            $pr = $payload['pull_request'] ?? [];
            $title = $pr['title'] ?? '';
            $body = $pr['body'] ?? '';
            $user = $pr['user']['login'] ?? 'github-bot';
            $merged = $pr['merged'] ?? false;

            // Extract task IDs like #1, #2, Fixes #3
            preg_match_all('/#(\d+)/', $title . ' ' . $body, $matches);
            $taskIds = array_unique($matches[1] ?? []);

            foreach ($taskIds as $id) {
                $task = Task::find($id);
                if ($task) {
                    if ($action === 'opened' || $action === 'reopened') {
                        $task->update(['status' => 'review']);
                        $desc = "GitHub PR \"{$title}\" by @{$user} opened. Task moved to Review.";
                    } elseif ($action === 'closed' && $merged) {
                        $task->update(['status' => 'done', 'completed_at' => now()]);
                        $desc = "GitHub PR \"{$title}\" merged by @{$user}. Task moved to Done.";
                    } else {
                        $desc = "GitHub PR \"{$title}\" updated for task #{$id}.";
                    }

                    ActivityLog::create([
                        'project_id' => $task->project_id,
                        'user_id' => $task->assigned_to,
                        'task_id' => $task->id,
                        'action' => 'moved',
                        'description' => $desc,
                    ]);

                    $affectedTasks[] = $task->id;
                }
            }
        } elseif ($event === 'push') {
            $commits = $payload['commits'] ?? [];
            foreach ($commits as $commit) {
                $msg = $commit['message'] ?? '';
                $author = $commit['author']['name'] ?? 'git-committer';

                preg_match_all('/#(\d+)/', $msg, $matches);
                $taskIds = array_unique($matches[1] ?? []);

                foreach ($taskIds as $id) {
                    $task = Task::find($id);
                    if ($task) {
                        if (str_contains(strtolower($msg), 'fix') || str_contains(strtolower($msg), 'close')) {
                            $task->update(['status' => 'done', 'completed_at' => now()]);
                            $desc = "Git commit by {$author}: \"{$msg}\". Task moved to Done.";
                        } else {
                            $task->update(['status' => 'in_progress']);
                            $desc = "Git commit linked by {$author}: \"{$msg}\". Task moved to In Progress.";
                        }

                        ActivityLog::create([
                            'project_id' => $task->project_id,
                            'user_id' => $task->assigned_to,
                            'task_id' => $task->id,
                            'action' => 'moved',
                            'description' => $desc,
                        ]);

                        $affectedTasks[] = $task->id;
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'event' => $event,
            'affected_tasks' => $affectedTasks,
        ]);
    }
}
