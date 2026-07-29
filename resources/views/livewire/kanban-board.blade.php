<div wire:poll.3s.keep-alive class="h-full flex gap-6 items-start pb-4 relative">
    
    <!-- SKELETON LAZY LOADING OVERLAY (Triggers ONLY on user actions like filtering/searching, NOT background polling) -->
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
        @endphp

        <!-- COLUMN CARD CONTAINER (20px-24px White Card Container) -->
        <div class="w-80 shrink-0 flex flex-col max-h-full rounded-[24px] bg-white dark:bg-[#1B182E] border border-[#ECE8F7] dark:border-[#2A2645] p-4 shadow-soft-card">
            
            <!-- Column Header -->
            <div class="px-2 py-2 mb-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full {{ $col['dot'] }} shadow-xs"></span>
                    <h3 class="font-extrabold text-sm {{ $colHeaderColor }} dark:text-white tracking-tight">{{ $col['title'] }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $colHeaderBg }} {{ $colHeaderColor }}">
                        {{ $tasks->count() }}
                    </span>
                </div>

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

            <!-- Column Task Drop Area (SortableJS Target) -->
            <div 
                data-status="{{ $col['key'] }}"
                x-init="
                    new Sortable($el, {
                        group: 'kanban-board',
                        animation: 220,
                        easing: 'cubic-bezier(1, 0, 0, 1)',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        handle: '.task-card',
                        onEnd(evt) {
                            const taskId = evt.item.getAttribute('data-task-id');
                            const targetStatus = evt.to.getAttribute('data-status');
                            const newIndex = evt.newIndex;
                            if (taskId) {
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
                    @endphp

                    <!-- TASK CARD (Modern Soft UI 20px Card) -->
                    <div 
                        data-task-id="{{ $task->id }}"
                        class="task-card group relative rounded-[20px] bg-white dark:bg-[#221F3B] border border-[#ECE8F7] dark:border-[#312C52] p-4 shadow-soft-card hover:shadow-card-hover hover:-translate-y-1 transition-all duration-200 cursor-grab active:cursor-grabbing"
                    >
                        <!-- Top Row: Priority Chip & Labels & Actions Menu -->
                        <div class="flex items-start justify-between gap-2 mb-2.5">
                            <div class="flex flex-wrap gap-1.5 items-center">
                                <x-priority-badge :priority="$task->priority" />
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

                        <!-- Card Footer: Due Date, Counts, Assignee Avatar -->
                        <div class="flex items-center justify-between pt-2.5 border-t border-[#ECE8F7] dark:border-[#312C52] text-xs">
                            <div class="flex items-center gap-3 text-[#7A7A92] font-semibold">
                                @if($task->due_date)
                                    <div class="flex items-center gap-1 text-[11px] {{ $task->isOverdue() ? 'text-[#FF6B81] font-bold' : 'text-[#7A7A92]' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>{{ $task->due_date->format('M d') }}</span>
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
</div>
