<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskLabel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Demo Users
        $alex = User::create([
            'name' => 'Alex Morgan',
            'email' => 'alex@kanban.test',
            'password' => Hash::make('password'),
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            'role' => 'Lead Designer',
        ]);

        $sarah = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@kanban.test',
            'password' => Hash::make('password'),
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=256&q=80',
            'role' => 'Fullstack Engineer',
        ]);

        $david = User::create([
            'name' => 'David Kim',
            'email' => 'david@kanban.test',
            'password' => Hash::make('password'),
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
            'role' => 'Product Manager',
        ]);

        $elena = User::create([
            'name' => 'Elena Rostova',
            'email' => 'elena@kanban.test',
            'password' => Hash::make('password'),
            'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=256&q=80',
            'role' => 'QA Specialist',
        ]);

        // 2. Create Project
        $project = Project::create([
            'name' => 'Linear Next-Gen Product Launch',
            'description' => 'Soft minimalist SaaS project management workspace engineered for fast software & design teams.',
        ]);

        // 3. Create Tasks for Columns

        // --- BACKLOG ---
        $t1 = Task::create([
            'project_id' => $project->id,
            'title' => 'AI Task Auto-Summarization & Insights',
            'description' => 'Integrate LLM API to generate automated standup reports and task summary badges on hover.',
            'status' => 'backlog',
            'position' => 0,
            'priority' => 'urgent',
            'due_date' => now()->addDays(5),
            'assigned_to' => $david->id,
            'created_by' => $alex->id,
        ]);
        $t1->labels()->createMany([
            ['label' => 'AI', 'color' => 'purple'],
            ['label' => 'Feature', 'color' => 'lavender'],
        ]);
        $t1->checklists()->createMany([
            ['title' => 'Design prompt template', 'completed' => true, 'position' => 0],
            ['title' => 'Setup background job queue', 'completed' => false, 'position' => 1],
            ['title' => 'Frontend Livewire streaming view', 'completed' => false, 'position' => 2],
        ]);

        $t2 = Task::create([
            'project_id' => $project->id,
            'title' => 'Mobile Dark Mode Soft UI Palette',
            'description' => 'Ensure Soft UI purple theme tokens switch dynamically on touch viewports.',
            'status' => 'backlog',
            'position' => 1,
            'priority' => 'medium',
            'due_date' => now()->addDays(12),
            'assigned_to' => $alex->id,
            'created_by' => $david->id,
        ]);
        $t2->labels()->createMany([
            ['label' => 'UI/UX', 'color' => 'pink'],
            ['label' => 'Mobile', 'color' => 'amber'],
        ]);

        // --- TO DO ---
        $t4 = Task::create([
            'project_id' => $project->id,
            'title' => 'Integrate Livewire 3 Drag & Drop with SortableJS',
            'description' => 'Connect Alpine x-sort event listeners to Livewire reorder methods for smooth reordering without page refreshes.',
            'status' => 'todo',
            'position' => 0,
            'priority' => 'high',
            'due_date' => now()->addDays(2),
            'assigned_to' => $sarah->id,
            'created_by' => $alex->id,
        ]);
        $t4->labels()->createMany([
            ['label' => 'Frontend', 'color' => 'purple'],
            ['label' => 'Livewire', 'color' => 'pink'],
        ]);
        $t4->checklists()->createMany([
            ['title' => 'Install sortablejs package', 'completed' => true, 'position' => 0],
            ['title' => 'Bind Alpine x-init listener on column container', 'completed' => true, 'position' => 1],
            ['title' => 'Dispatch Livewire updateTaskOrder event', 'completed' => true, 'position' => 2],
            ['title' => 'Add ghost element CSS animations', 'completed' => false, 'position' => 3],
        ]);

        $t5 = Task::create([
            'project_id' => $project->id,
            'title' => 'Design Soft Pastel Glassmorphism Modals',
            'description' => 'Craft modern rounded 20px modals with soft backdrop blur and keyboard shortcut triggers.',
            'status' => 'todo',
            'position' => 1,
            'priority' => 'medium',
            'due_date' => now()->addDays(3),
            'assigned_to' => $alex->id,
            'created_by' => $david->id,
        ]);
        $t5->labels()->createMany([
            ['label' => 'UI/UX', 'color' => 'pink'],
            ['label' => 'Design', 'color' => 'lavender'],
        ]);

        // --- IN PROGRESS ---
        $t7 = Task::create([
            'project_id' => $project->id,
            'title' => 'Optimize SQLite Database Indexes & Query Performance',
            'description' => 'Index (project_id, status, position) composite keys for instant column queries.',
            'status' => 'in_progress',
            'position' => 0,
            'priority' => 'urgent',
            'due_date' => now()->addDays(1),
            'assigned_to' => $sarah->id,
            'created_by' => $sarah->id,
        ]);
        $t7->labels()->createMany([
            ['label' => 'Database', 'color' => 'purple'],
            ['label' => 'Performance', 'color' => 'emerald'],
        ]);
        $t7->checklists()->createMany([
            ['title' => 'Add composite index in migration', 'completed' => true, 'position' => 0],
            ['title' => 'Benchmark 1000 tasks query time', 'completed' => true, 'position' => 1],
        ]);

        // --- REVIEW ---
        $t8 = Task::create([
            'project_id' => $project->id,
            'title' => 'Code Review: Modern Soft UI Card Elevation & Shadows',
            'description' => 'Review 20px border radius, subtle hover lift effects, and #6E63D9 purple theme implementation.',
            'status' => 'review',
            'position' => 0,
            'priority' => 'high',
            'due_date' => now()->addDays(1),
            'assigned_to' => $elena->id,
            'created_by' => $alex->id,
        ]);
        $t8->labels()->createMany([
            ['label' => 'QA', 'color' => 'pink'],
            ['label' => 'Review', 'color' => 'lavender'],
        ]);

        // --- DONE ---
        $t9 = Task::create([
            'project_id' => $project->id,
            'title' => 'Setup Laravel 12 & Vite Build Pipeline',
            'description' => 'Initialize Tailwind CSS v4 and Alpine.js asset compiler.',
            'status' => 'done',
            'position' => 0,
            'priority' => 'urgent',
            'due_date' => now()->subDays(1),
            'completed_at' => now()->subHours(6),
            'assigned_to' => $sarah->id,
            'created_by' => $sarah->id,
        ]);
        $t9->labels()->create(['label' => 'Infrastructure', 'color' => 'purple']);

        // 4. Create Activity Logs
        ActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $sarah->id,
            'task_id' => $t9->id,
            'action' => 'completed',
            'description' => 'Completed task "Setup Laravel 12 & Vite Build Pipeline"',
        ]);

        ActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $elena->id,
            'task_id' => $t8->id,
            'action' => 'moved',
            'description' => 'Moved task "Code Review: Modern Soft UI Card Elevation" to Review',
        ]);
    }
}
