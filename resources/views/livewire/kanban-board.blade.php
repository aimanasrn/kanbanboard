<div class="h-full flex gap-6 items-start pb-4 relative">
    
    <!-- SKELETON LAZY LOADING OVERLAY (ONLY for global search & filters) -->
    <div wire:loading.delay.shortest wire:target="search, priorityFilter, assigneeFilter, dueDateFilter, labelFilter" class="absolute inset-0 z-20 flex gap-6 items-start bg-[#F8F5FF]/80 dark:bg-[#12101F]/80 backdrop-blur-xs transition-all">
        @foreach(['Backlog', 'To Do', 'In Progress', 'Review', 'Done'] as $skCol)
            <div class="w-80 shrink-0 flex flex-col rounded-[24px] bg-white dark:bg-[#1B182E] border border-[#ECE8F7] dark:border-[#2A2645] p-4 shadow-soft-card space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-[#ECE8F7] dark:border-[#2A2645]">
                    <div class="h-4 w-24 rounded-lg skeleton-shimmer"></div>
                    <div class="h-6 w-6 rounded-full skeleton-shimmer"></div>
                </div>
                <x-skeleton-card />
                <x-skeleton-card />
                <x-skeleton-card />
            </div>
        @endforeach
    </div>

    @foreach($columns as $col)
        @php
            $tasks = $tasksByColumn[$col['key']] ?? collect();

            $colHeaderColor = match($col['key']) {
                'backlog' => 'text-[#6E63D9]',
                'todo' => 'text-blue-600',
                'in_progress' => 'text-amber-600',
                'review' => 'text-[#A98BEF]',
                'done' => 'text-[#72D49A]',
                default => 'text-[#2F2F45]',
            };

            $colHeaderBg = match($col['key']) {
                'backlog' => 'bg-[#6E63D9]/10',
                'todo' => 'bg-blue-500/10',
                'in_progress' => 'bg-amber-500/10',
                'review' => 'bg-[#A98BEF]/15',
                'done' => 'bg-[#72D49A]/20',
                default => 'bg-slate-100',
            };

            $columnTaskIds = $tasks->pluck('id')->toArray();
            $selectedInColumn = array_intersect($columnTaskIds, $selectedTaskIds);
            $allColumnSelected = count($columnTaskIds) > 0 && count($selectedInColumn) === count($columnTaskIds);

            // WIP Limit logic
            $wipLimit = $project ? $project->getWipLimit($col['key']) : 0;
            $isOverWip = $wipLimit > 0 && $tasks->count() > $wipLimit;
            $isMaxWip = $wipLimit > 0 && $tasks->count() == $wipLimit;
        @endphp

        <!-- COLUMN CARD CONTAINER -->
        <div wire:key="col-{{ $col['key'] }}" class="w-80 shrink-0 flex flex-col max-h-full rounded-[24px] bg-white dark:bg-[#1B182E] border p-4 shadow-soft-card transition-all duration-300
                    {{ $isOverWip ? 'border-[#FF6B81] ring-2 ring-[#FF6B81]/40 bg-[#FF6B81]/5' : 'border-[#ECE8F7] dark:border-[#2A2645]' }}">
            
            <!-- Column Header -->
            <div class="px-2 py-2 mb-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <button 
                        wire:click="selectAllInColumn('{{ $col['key'] }}')"
                        class="w-4 h-4 rounded border transition-all flex items-center justify-center {{ $allColumnSelected ? 'bg-[#6E63D9] border-[#6E63D9] text-white' : (count($selectedInColumn) > 0 ? 'bg-[#6E63D9]/30 border-[#6E63D9] text-white' : 'border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9]') }}"
                        title="{{ $allColumnSelected ? 'Deselect column' : 'Select all in column' }}"
                    >
                        @if($allColumnSelected)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @elseif(count($selectedInColumn) > 0)
                            <span class="w-2 h-0.5 bg-[#6E63D9]"></span>
                        @endif
                    </button>

                    <span class="w-2.5 h-2.5 rounded-full {{ $col['dot'] }} shadow-xs"></span>
                    <h3 class="font-extrabold text-sm {{ $colHeaderColor }} dark:text-white tracking-tight">{{ $col['title'] }}</h3>
                    
                    <!-- WIP Limit Badge -->
                    @if($isOverWip)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-[#FF6B81] text-white animate-pulse shadow-sm flex items-center gap-1" title="WIP Limit Exceeded! Max {{ $wipLimit }} tasks allowed.">
                            <span>{{ $tasks->count() }}/{{ $wipLimit }}</span>
                            <span class="text-[10px]">⚠️</span>
                        </span>
                    @elseif($isMaxWip)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-500/20 text-amber-600 border border-amber-500/30" title="WIP Limit Reached (Full)">
                            {{ $tasks->count() }}/{{ $wipLimit }}
                        </span>
                    @elseif($wipLimit > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $colHeaderBg }} {{ $colHeaderColor }}">
                            {{ $tasks->count() }}/{{ $wipLimit }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $colHeaderBg }} {{ $colHeaderColor }}">
                            {{ $tasks->count() }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <!-- Column WIP Limit Settings Gear -->
                    <button 
                        wire:click="openWipModal('{{ $col['key'] }}')"
                        class="w-7 h-7 rounded-full bg-[#F8F5FF] dark:bg-[#25203D] text-[#7A7A92] hover:text-[#6E63D9] hover:bg-[#6E63D9]/10 transition-all flex items-center justify-center shadow-xs"
                        title="Set WIP limit for {{ $col['title'] }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>

                    <!-- Add Task Button for Column -->
                    <button 
                        wire:click="$dispatch('open-task-modal', { status: '{{ $col['key'] }}' })"
                        class="w-7 h-7 rounded-full bg-[#F8F5FF] dark:bg-[#25203D] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all flex items-center justify-center shadow-xs"
                        title="Add task to {{ $col['title'] }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Column Task Drop Area (SortableJS Target) -->
            <div 
                data-status="{{ $col['key'] }}"
                wire:key="col-tasks-{{ $col['key'] }}"
                x-init="
                    new Sortable($el, {
                        group: 'kanban-board',
                        animation: 200,
                        easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        handle: '.task-card',
                        onEnd(evt) {
                            const taskId = evt.item.getAttribute('data-task-id');
                            const targetStatus = evt.to.getAttribute('data-status');
                            const newIndex = evt.newIndex;
                            if (taskId && (evt.from !== evt.to || evt.oldIndex !== evt.newIndex)) {
                                // Instant skeleton pulse loading feedback on dropped card during reorder CRUD
                                evt.item.classList.add('animate-pulse', 'opacity-60');
                                Livewire.dispatch('reorder-task', { taskId: parseInt(taskId), newStatus: targetStatus, newPosition: newIndex });
                            }
                        }
                    })
                "
                class="flex-1 overflow-y-auto space-y-3.5 custom-scrollbar min-h-[160px] pr-1"
            >
                @forelse($tasks as $task)
                    @php
                        $progress = $task->checklist_progress;
                        $isSelected = in_array($task->id, $selectedTaskIds);
                    @endphp

                    <!-- TASK CARD -->
                    <div 
                        wire:key="task-card-{{ $task->id }}"
                        data-task-id="{{ $task->id }}"
                        class="task-card group relative rounded-[20px] bg-white dark:bg-[#221F3B] border p-4 shadow-soft-card hover:shadow-card-hover hover:-translate-y-1 transition-all duration-200 cursor-grab active:cursor-grabbing overflow-hidden
                               {{ $isSelected ? 'border-[#6E63D9] ring-2 ring-[#6E63D9]/50 bg-[#6E63D9]/5 dark:bg-[#6E63D9]/10' : 'border-[#ECE8F7] dark:border-[#312C52]' }}"
                    >
                        <!-- Card Skeleton Loading Overlay for individual card CRUD actions -->
                        <div wire:loading wire:target="toggleSelectTask({{ $task->id }})" 
                             class="absolute inset-0 z-20 bg-white/95 dark:bg-[#221F3B]/95 p-4 rounded-[20px] shadow-soft-card flex flex-col justify-between space-y-3 animate-pulse border border-[#6E63D9]/40">
                            <div class="flex items-center justify-between">
                                <div class="flex gap-1.5">
                                    <div class="h-4 w-12 rounded-full skeleton-shimmer"></div>
                                    <div class="h-4 w-10 rounded-full skeleton-shimmer"></div>
                                </div>
                                <div class="w-4 h-4 rounded-full skeleton-shimmer"></div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="h-4 w-3/4 rounded-lg skeleton-shimmer"></div>
                                <div class="h-3 w-full rounded-md skeleton-shimmer"></div>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-[#ECE8F7]/50 dark:border-[#312C52]/50">
                                <div class="h-3 w-16 rounded skeleton-shimmer"></div>
                                <div class="w-6 h-6 rounded-full skeleton-shimmer"></div>
                            </div>
                        </div>

                        <!-- Card Skeleton Loading Overlay during Bulk CRUD operations on selected tasks -->
                        @if($isSelected)
                            <div wire:loading wire:target="bulkMoveStatus, bulkAssign, bulkSetPriority, bulkArchive, bulkDeleteConfirmed" 
                                 class="absolute inset-0 z-20 bg-white/95 dark:bg-[#221F3B]/95 p-4 rounded-[20px] shadow-soft-card flex flex-col justify-between space-y-3 animate-pulse border border-[#6E63D9]/40">
                                <div class="flex items-center justify-between">
                                    <div class="flex gap-1.5">
                                        <div class="h-4 w-12 rounded-full skeleton-shimmer"></div>
                                        <div class="h-4 w-10 rounded-full skeleton-shimmer"></div>
                                    </div>
                                    <div class="w-4 h-4 rounded-full skeleton-shimmer"></div>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="h-4 w-3/4 rounded-lg skeleton-shimmer"></div>
                                    <div class="h-3 w-full rounded-md skeleton-shimmer"></div>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-[#ECE8F7]/50 dark:border-[#312C52]/50">
                                    <div class="h-3 w-16 rounded skeleton-shimmer"></div>
                                    <div class="w-6 h-6 rounded-full skeleton-shimmer"></div>
                                </div>
                            </div>
                        @endif

                        <!-- Top Row: Checkbox, Priority Chip & Labels & Actions Menu -->
                        <div class="flex items-start justify-between gap-2 mb-2.5">
                            <div class="flex flex-wrap gap-1.5 items-center">
                                <!-- Task Selection Checkbox -->
                                <button 
                                    wire:click="toggleSelectTask({{ $task->id }})"
                                    class="w-4 h-4 rounded border transition-all flex items-center justify-center shrink-0 mr-1 {{ $isSelected ? 'bg-[#6E63D9] border-[#6E63D9] text-white' : 'border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9] text-transparent' }}"
                                    title="{{ $isSelected ? 'Deselect task' : 'Select task' }}"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </button>

                                <x-priority-badge :priority="$task->priority" />
                                
                                @if($task->recurring_frequency)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20" title="Recurring {{ $task->recurring_frequency }} task">
                                        🔄 {{ ucfirst($task->recurring_frequency) }}
                                    </span>
                                @endif

                                <!-- Live Timer Start / Pause Button -->
                                @if($task->isTimerRunning())
                                    <button 
                                        wire:click="toggleTimer({{ $task->id }})"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black bg-[#FF6B81] text-white animate-pulse flex items-center gap-1 shadow-sm"
                                        title="Stop working timer"
                                    >
                                        <span>⏸️</span>
                                        <span>TIMING</span>
                                    </button>
                                @else
                                    <button 
                                        wire:click="toggleTimer({{ $task->id }})"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#6E63D9]/10 hover:bg-[#6E63D9] text-[#6E63D9] hover:text-white transition-all flex items-center gap-1 opacity-70 hover:opacity-100"
                                        title="Start working timer"
                                    >
                                        <span>▶️</span>
                                        <span>Timer</span>
                                    </button>
                                @endif

                                @foreach($task->labels as $label)
                                    <x-label-badge :label="$label->label" :color="$label->color" />
                                @endforeach
                            </div>

                            <!-- Card Dropdown Menu -->
                            <div x-data="{ open: false }" class="shrink-0 relative">
                                <button 
                                    @click="open = !open" 
                                    @click.outside="open = false"
                                    class="p-1 rounded-full text-[#7A7A92] hover:text-[#6E63D9] hover:bg-[#F8F5FF] transition-all"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                                
                                <div x-show="open" x-transition class="absolute right-0 top-8 z-30 w-36 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] shadow-2xl py-1 text-xs text-[#2F2F45] dark:text-[#F2EEFF]">
                                    <button wire:click="$dispatch('open-task-modal', { taskId: {{ $task->id }} })" class="w-full text-left px-3.5 py-2 hover:bg-[#6E63D9]/10 hover:text-[#6E63D9] font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit Task</span>
                                    </button>
                                    <button wire:click="$dispatch('duplicate-task', { taskId: {{ $task->id }} })" class="w-full text-left px-3.5 py-2 hover:bg-[#6E63D9]/10 hover:text-[#6E63D9] font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Duplicate</span>
                                    </button>
                                    <button wire:click="$dispatch('confirm-delete-task', { taskId: {{ $task->id }} })" class="w-full text-left px-3.5 py-2 hover:bg-rose-50 text-[#FF6B81] font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Task Title & Description -->
                        <h4 
                            wire:click="$dispatch('open-task-modal', { taskId: {{ $task->id }} })"
                            class="font-extrabold text-sm text-[#2F2F45] dark:text-white group-hover:text-[#6E63D9] transition-colors leading-snug cursor-pointer mb-1"
                        >
                            {{ $task->title }}
                        </h4>

                        @if($task->description)
                            <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] line-clamp-2 mb-3 leading-relaxed font-normal">
                                {{ $task->description }}
                            </p>
                        @endif

                        <!-- Subtask Checklist Progress -->
                        @if($progress['total'] > 0)
                            <div class="mb-3 space-y-1 bg-[#F8F5FF] dark:bg-[#1B182E] p-2.5 rounded-xl border border-[#ECE8F7] dark:border-[#2A2645]">
                                <div class="flex items-center justify-between text-[11px] font-bold text-[#7A7A92]">
                                    <span>Checklist</span>
                                    <span class="text-[#6E63D9] font-extrabold">{{ $progress['completed'] }}/{{ $progress['total'] }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-[#ECE8F7] dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#6E63D9] to-[#E98AC9] rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endif

                        <!-- Card Footer: Due Date, Hours, Counts, Assignee Avatar -->
                        <div class="flex items-center justify-between pt-2.5 border-t border-[#ECE8F7] dark:border-[#312C52] text-xs">
                            <div class="flex items-center gap-2.5 flex-wrap text-[#7A7A92] font-semibold">
                                @if($task->due_date)
                                    <div class="flex items-center gap-1 text-[11px] {{ $task->isOverdue() ? 'text-[#FF6B81] font-bold' : 'text-[#7A7A92]' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>{{ $task->due_date->format('M d') }}</span>
                                    </div>
                                @endif

                                <!-- Hours Tracking Pill -->
                                @if($task->estimated_hours || $task->actual_hours)
                                    <div class="flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#6E63D9]/10 text-[#6E63D9] dark:text-[#A98BEF] border border-[#6E63D9]/20" title="Spent hours / Estimated hours">
                                        <span>⏱️</span>
                                        <span>{{ $task->actual_hours ?? 0 }}h / {{ $task->estimated_hours ?? 0 }}h</span>
                                    </div>
                                @endif

                                @if($task->comments->count() > 0)
                                    <div class="flex items-center gap-1 text-[11px]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                        <span>{{ $task->comments->count() }}</span>
                                    </div>
                                @endif

                                @if($task->attachments->count() > 0)
                                    <div class="flex items-center gap-1 text-[11px]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span>{{ $task->attachments->count() }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Assignee Avatar -->
                            <div>
                                @if($task->assignee)
                                    <img src="{{ $task->assignee->avatar_url }}" class="w-7 h-7 rounded-full object-cover ring-2 ring-[#6E63D9]/20 shadow-xs" title="{{ $task->assignee->name }}" alt="{{ $task->assignee->name }}">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-[#F8F5FF] dark:bg-slate-800 border border-dashed border-[#A98BEF] flex items-center justify-center text-[10px] text-[#6E63D9] font-extrabold" title="Unassigned">
                                        ?
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- Empty State for Column -->
                    <div class="py-8 px-4 text-center border-2 border-dashed border-[#ECE8F7] dark:border-[#352F52] rounded-[20px]">
                        <p class="text-xs font-semibold text-[#7A7A92]">No tasks in {{ $col['title'] }}</p>
                        <button 
                            wire:click="$dispatch('open-task-modal', { status: '{{ $col['key'] }}' })"
                            class="mt-2 text-xs font-bold text-[#6E63D9] hover:underline"
                        >
                            + Add Task
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Quick Add Button at Bottom of Column -->
            <div class="pt-3">
                <button 
                    wire:click="$dispatch('open-task-modal', { status: '{{ $col['key'] }}' })"
                    class="w-full py-2.5 px-4 rounded-full bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9] text-[#6E63D9] hover:text-white text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 group shadow-xs"
                >
                    <svg class="w-4 h-4 text-[#6E63D9] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Add Task</span>
                </button>
            </div>

        </div>
    @endforeach

    <!-- FLOATING BULK ACTIONS TOOLBAR -->
    @if(count($selectedTaskIds) > 0)
        <div 
            x-data 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-10 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-95"
            class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-6 py-3 rounded-[24px] bg-white/95 dark:bg-[#1C1830]/95 border border-[#6E63D9]/30 shadow-2xl backdrop-blur-md text-xs font-bold text-[#2F2F45] dark:text-white max-w-4xl overflow-x-auto custom-scrollbar"
        >
            <!-- Badge Count -->
            <div class="flex items-center gap-2 pr-3 border-r border-[#ECE8F7] dark:border-[#2A2545]">
                <span class="w-6 h-6 rounded-full bg-[#6E63D9] text-white flex items-center justify-center text-xs font-extrabold shadow-sm">
                    {{ count($selectedTaskIds) }}
                </span>
                <span class="hidden sm:inline font-extrabold">selected</span>
            </div>

            <!-- Bulk Move Status Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button 
                    @click="open = !open" 
                    class="px-3 py-1.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9]/10 text-[#6E63D9] transition-all flex items-center gap-1.5 font-bold"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Move Status</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div 
                    x-show="open" 
                    x-transition 
                    class="absolute bottom-full left-0 mb-2 w-44 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] shadow-xl py-1 text-xs z-50"
                >
                    <button wire:click="bulkMoveStatus('backlog')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-[#6E63D9]/10 text-[#6E63D9] font-bold">Backlog</button>
                    <button wire:click="bulkMoveStatus('todo')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 text-blue-600 font-bold">To Do</button>
                    <button wire:click="bulkMoveStatus('in_progress')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-amber-50 text-amber-600 font-bold">In Progress</button>
                    <button wire:click="bulkMoveStatus('review')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-purple-50 text-purple-600 font-bold">Review</button>
                    <button wire:click="bulkMoveStatus('done')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-emerald-600 font-bold">Done</button>
                </div>
            </div>

            <!-- Bulk Assign Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button 
                    @click="open = !open" 
                    class="px-3 py-1.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9]/10 text-[#6E63D9] transition-all flex items-center gap-1.5 font-bold"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Assign</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div 
                    x-show="open" 
                    x-transition 
                    class="absolute bottom-full left-0 mb-2 w-48 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] shadow-xl py-1 text-xs z-50 max-h-48 overflow-y-auto custom-scrollbar"
                >
                    <button wire:click="bulkAssign(null)" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 text-[#7A7A92] font-bold">Unassigned</button>
                    @foreach($allUsers as $u)
                        <button wire:click="bulkAssign({{ $u->id }})" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-[#6E63D9]/10 hover:text-[#6E63D9] font-bold truncate">
                            {{ $u->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Bulk Priority Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button 
                    @click="open = !open" 
                    class="px-3 py-1.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9]/10 text-[#6E63D9] transition-all flex items-center gap-1.5 font-bold"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Priority</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div 
                    x-show="open" 
                    x-transition 
                    class="absolute bottom-full left-0 mb-2 w-36 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] shadow-xl py-1 text-xs z-50"
                >
                    <button wire:click="bulkSetPriority('urgent')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-rose-50 text-[#FF6B81] font-bold">Urgent</button>
                    <button wire:click="bulkSetPriority('high')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-amber-50 text-amber-600 font-bold">High</button>
                    <button wire:click="bulkSetPriority('medium')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 text-blue-600 font-bold">Medium</button>
                    <button wire:click="bulkSetPriority('low')" @click="open = false" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 text-slate-600 font-bold">Low</button>
                </div>
            </div>

            <!-- Bulk Archive Button -->
            <button 
                wire:click="bulkArchive" 
                class="px-3 py-1.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9]/10 text-[#6E63D9] transition-all flex items-center gap-1.5 font-bold"
                title="Archive selected tasks"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span class="hidden md:inline">Archive</span>
            </button>

            <!-- Bulk Delete Button -->
            <button 
                wire:click="confirmBulkDelete" 
                class="px-3 py-1.5 rounded-xl bg-[#FF6B81]/10 hover:bg-[#FF6B81] text-[#FF6B81] hover:text-white transition-all flex items-center gap-1.5 font-bold"
                title="Delete selected tasks"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="hidden md:inline">Delete</span>
            </button>

            <!-- Clear Selection -->
            <button 
                wire:click="clearSelection" 
                class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-[#7A7A92] transition-all ml-1"
                title="Clear selection"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- BULK DELETE CONFIRMATION MODAL -->
    @if($showBulkDeleteModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(18,16,31,0.6); backdrop-filter: blur(8px);"
        >
            <div class="w-full max-w-sm rounded-[24px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-[#FF6B81]/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-[#FF6B81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white mb-2">Delete {{ count($selectedTaskIds) }} Tasks?</h3>
                <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] mb-6">
                    Are you sure you want to permanently delete these selected tasks? This action cannot be undone.
                </p>
                <div class="flex items-center gap-3 justify-center">
                    <button 
                        wire:click="$set('showBulkDeleteModal', false)"
                        class="px-5 py-2 rounded-full bg-[#ECE8F7] dark:bg-[#25203D] text-[#7A7A92] font-bold text-xs hover:bg-[#DDD8F0] transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="bulkDeleteConfirmed"
                        class="px-5 py-2 rounded-full bg-gradient-to-r from-[#FF6B81] to-[#FF4560] text-white font-bold text-xs shadow-lg hover:shadow-xl transition-all"
                    >
                        Delete All
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT WIP LIMIT MODAL -->
    @if($showWipModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(18,16,31,0.6); backdrop-filter: blur(8px);"
        >
            <div class="w-full max-w-sm rounded-[24px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl p-6 text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#6E63D9]/10 flex items-center justify-center mx-auto mb-3 text-[#6E63D9]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white mb-1">Set Column WIP Limit</h3>
                <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] mb-4">
                    Column: <span class="font-bold text-[#6E63D9] uppercase">{{ $editingWipColumn }}</span> (Set 0 for unlimited)
                </p>

                <div class="mb-5">
                    <input 
                        type="number" 
                        min="0"
                        wire:model="editingWipLimit"
                        class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-sm font-extrabold text-center focus:outline-none focus:border-[#6E63D9]"
                        placeholder="0"
                    />
                </div>

                <div class="flex items-center gap-3 justify-center">
                    <button 
                        wire:click="$set('showWipModal', false)"
                        class="px-5 py-2 rounded-full bg-[#ECE8F7] dark:bg-[#25203D] text-[#7A7A92] font-bold text-xs hover:bg-[#DDD8F0] transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="saveWipLimit"
                        class="px-6 py-2 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] text-white font-bold text-xs shadow-lg hover:shadow-xl transition-all"
                    >
                        Save Limit
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
