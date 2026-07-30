<div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-[#12101F]/60 backdrop-blur-md animate-fadeIn" @keydown.escape.window="$wire.dispatch('close-task-modal')">
    <div class="bg-white dark:bg-[#1B182E] max-w-2xl w-full rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-[#ECE8F7] dark:border-[#2A2645] flex items-center justify-between bg-[#F8F5FF] dark:bg-[#151326] shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-[#6E63D9]/10 text-[#6E63D9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-[#2F2F45] dark:text-white">
                        {{ $taskId ? 'Edit Task Details' : 'Create New Task' }}
                    </h2>
                    <p class="text-xs text-[#7A7A92] font-medium">Manage task content, checklists, labels & activity logs.</p>
                </div>
            </div>

            <button wire:click="$dispatch('close-task-modal')" class="p-1.5 rounded-full text-[#7A7A92] hover:text-[#2F2F45] hover:bg-[#ECE8F7]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 border-b border-[#ECE8F7] dark:border-[#2A2645] bg-white dark:bg-[#1B182E] flex items-center gap-6 text-xs font-semibold shrink-0">
            <button 
                wire:click="$set('activeTab', 'details')" 
                class="py-3 border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'details' ? 'border-[#6E63D9] text-[#6E63D9] font-bold' : 'border-transparent text-[#7A7A92] hover:text-[#2F2F45]' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span>Task Details</span>
            </button>

            <button 
                wire:click="$set('activeTab', 'checklists')" 
                class="py-3 border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'checklists' ? 'border-[#6E63D9] text-[#6E63D9] font-bold' : 'border-transparent text-[#7A7A92] hover:text-[#2F2F45]' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Checklist ({{ count($checklists) }})</span>
            </button>

            @if($taskId)
                <button 
                    wire:click="$set('activeTab', 'attachments')" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'attachments' ? 'border-[#6E63D9] text-[#6E63D9] font-bold' : 'border-transparent text-[#7A7A92] hover:text-[#2F2F45]' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <span>Attachments ({{ $task ? $task->attachments->count() : 0 }})</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'comments')" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'comments' ? 'border-[#6E63D9] text-[#6E63D9] font-bold' : 'border-transparent text-[#7A7A92] hover:text-[#2F2F45]' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <span>Comments ({{ $task ? $task->comments->count() : 0 }})</span>
                </button>

                <button 
                    wire:click="$set('activeTab', 'activity')" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'activity' ? 'border-[#6E63D9] text-[#6E63D9] font-bold' : 'border-transparent text-[#7A7A92] hover:text-[#2F2F45]' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Activity Audit</span>
                </button>
            @endif
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-5 custom-scrollbar">

            <!-- TAB 1: DETAILS -->
            @if($activeTab === 'details')
                <!-- Task Title Input -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Task Title <span class="text-[#FF6B81]">*</span></label>
                    <input 
                        type="text" 
                        wire:model="title" 
                        placeholder="e.g. Design Soft Minimalist Dashboard Components"
                        class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-sm font-semibold focus:outline-none focus:border-[#6E63D9]"
                    />
                    @error('title') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- Description Textarea -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Description</label>
                    <textarea 
                        wire:model="description" 
                        rows="4" 
                        placeholder="Add task description or design requirements..."
                        class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-medium focus:outline-none focus:border-[#6E63D9] resize-none"
                    ></textarea>
                </div>

                <!-- Status, Priority, Assignee Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Column Status -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Column Status</label>
                        <select wire:model="status" class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]">
                            <option value="backlog">Backlog</option>
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Review</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Priority Level</label>
                        <select wire:model="priority" class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]">
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <!-- Assigned Member -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Assign Member</label>
                        <select wire:model="assigned_to" class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Due Date</label>
                        <input 
                            type="datetime-local" 
                            wire:model="due_date" 
                            class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]"
                        />
                    </div>

                    <!-- Estimated Hours -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Estimated Hours (hrs)</label>
                        <input 
                            type="number" 
                            step="0.5"
                            min="0"
                            wire:model="estimated_hours" 
                            placeholder="e.g. 4.5"
                            class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]"
                        />
                    </div>

                    <!-- Spent / Actual Hours -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Spent Hours (hrs)</label>
                        <input 
                            type="number" 
                            step="0.5"
                            min="0"
                            wire:model="actual_hours" 
                            placeholder="e.g. 3.0"
                            class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]"
                        />
                    </div>

                    <!-- Recurring Frequency -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Repeat Task 🔄</label>
                        <select wire:model="recurring_frequency" class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-bold focus:outline-none focus:border-[#6E63D9]">
                            <option value="">Do Not Repeat</option>
                            <option value="daily">Every Day (Daily)</option>
                            <option value="weekly">Every Week (Weekly)</option>
                            <option value="monthly">Every Month (Monthly)</option>
                        </select>
                    </div>
                </div>

                <!-- Labels Manager Section -->
                <div class="space-y-2 pt-2 border-t border-[#ECE8F7] dark:border-[#2A2645]">
                    <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Labels / Tags</label>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @foreach($labels as $idx => $lbl)
                            <div class="inline-flex items-center gap-1">
                                <x-label-badge :label="$lbl['label']" :color="$lbl['color'] ?? 'purple'" />
                                <button type="button" wire:click="removeLabel({{ $idx }})" class="text-[#7A7A92] hover:text-[#FF6B81]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            wire:model="newLabelText" 
                            wire:keydown.enter.prevent="addLabel"
                            placeholder="Add tag..." 
                            class="flex-1 px-3.5 py-2 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-medium text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                        />
                        <select wire:model="newLabelColor" class="px-3 py-2 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-bold text-[#2F2F45] dark:text-white">
                            <option value="purple">Purple</option>
                            <option value="lavender">Lavender</option>
                            <option value="pink">Pink</option>
                            <option value="emerald">Emerald</option>
                            <option value="amber">Amber</option>
                            <option value="blue">Blue</option>
                        </select>
                        <button type="button" wire:click="addLabel" class="px-4 py-2 rounded-full bg-[#6E63D9] text-white text-xs font-bold shadow-button">
                            + Tag
                        </button>
                    </div>
                </div>
            @endif

            <!-- TAB 2: CHECKLISTS -->
            @if($activeTab === 'checklists')
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            wire:model="newChecklistTitle" 
                            wire:keydown.enter.prevent="addChecklistItem"
                            placeholder="Add subtask..." 
                            class="flex-1 px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-medium text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                        />
                        <button type="button" wire:click="addChecklistItem" class="px-4 py-2.5 rounded-full bg-[#6E63D9] text-white text-xs font-bold shadow-button">
                            + Add Item
                        </button>
                    </div>

                    <div class="space-y-2">
                        @forelse($checklists as $idx => $chk)
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] group">
                                <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleChecklist({{ $idx }})"
                                        @checked($chk['completed'] ?? false)
                                        class="w-4 h-4 rounded-md text-[#6E63D9] focus:ring-[#6E63D9]"
                                    />
                                    <span class="text-xs font-semibold text-[#2F2F45] dark:text-slate-200 {{ ($chk['completed'] ?? false) ? 'line-through text-[#7A7A92]' : '' }}">
                                        {{ $chk['title'] }}
                                    </span>
                                </label>
                                <button type="button" wire:click="removeChecklist({{ $idx }})" class="p-1 text-[#7A7A92] hover:text-[#FF6B81] opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-center text-xs text-[#7A7A92] py-6 font-medium">No checklist items added yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- TAB 3: ATTACHMENTS -->
            @if($activeTab === 'attachments' && $taskId)
                <div class="space-y-4">
                    <div class="p-6 border-2 border-dashed border-[#ECE8F7] dark:border-[#352F52] rounded-[20px] text-center bg-[#F8F5FF] dark:bg-[#25203D]">
                        <svg class="w-8 h-8 text-[#6E63D9] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-xs text-[#2F2F45] dark:text-slate-200 font-bold mb-1">Click to upload attachments</p>
                        <input type="file" wire:model="newAttachments" multiple class="mt-3 text-xs text-[#7A7A92] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#6E63D9] file:text-white hover:file:bg-[#5C52C7] cursor-pointer" />
                        @if(!empty($newAttachments))
                            <button type="button" wire:click="uploadAttachments" class="mt-3 px-4 py-2 rounded-full bg-[#6E63D9] text-white text-xs font-bold shadow-button">Confirm Upload</button>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @if($task && $task->attachments->count() > 0)
                            @foreach($task->attachments as $att)
                                <div class="flex items-center justify-between p-3 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-xl bg-[#6E63D9]/10 text-[#6E63D9]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <a href="{{ $att->url }}" target="_blank" class="font-bold text-[#2F2F45] dark:text-white hover:text-[#6E63D9] underline">{{ $att->original_name }}</a>
                                            <p class="text-[10px] text-[#7A7A92] font-semibold">{{ $att->formatted_size }}</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="deleteAttachment({{ $att->id }})" class="p-1 text-[#7A7A92] hover:text-[#FF6B81]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <!-- TAB 4: COMMENTS -->
            @if($activeTab === 'comments' && $taskId)
                @livewire('comment-section', ['taskId' => $taskId], key('comment-section-'.$taskId))
            @endif

            <!-- TAB 5: ACTIVITY AUDIT TRAIL -->
            @if($activeTab === 'activity' && $taskId)
                <div class="space-y-3">
                    <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white">Task Audit History</h4>
                    <div class="space-y-2">
                        @forelse($taskActivities as $act)
                            <div class="flex items-start gap-3 p-3 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52]">
                                <img src="{{ $act->user ? $act->user->avatar_url : 'https://ui-avatars.com/api/?name=User' }}" class="w-7 h-7 rounded-full object-cover shrink-0 mt-0.5" alt="">
                                <div class="flex-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-[#2F2F45] dark:text-white">{{ $act->user ? $act->user->name : 'System' }}</span>
                                        <span class="text-[10px] text-[#7A7A92] font-semibold">{{ $act->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[#7A7A92] dark:text-slate-300 mt-0.5 font-medium leading-relaxed">{{ $act->description }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-[#7A7A92] py-8 font-medium">No activity audit history for this task yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-[#ECE8F7] dark:border-[#2A2645] bg-[#F8F5FF] dark:bg-[#151326] flex items-center justify-between shrink-0">
            <span class="text-[11px] text-[#7A7A92] font-semibold">Press Esc to close</span>

            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    wire:click="$dispatch('close-task-modal')" 
                    class="py-2.5 px-4 rounded-full bg-white dark:bg-[#25203D] text-[#7A7A92] dark:text-slate-300 text-xs font-bold border border-[#ECE8F7] dark:border-[#352F52] transition-all"
                >
                    Cancel
                </button>

                <button 
                    type="button" 
                    wire:click="save" 
                    class="py-2.5 px-6 rounded-full bg-[#6E63D9] hover:bg-[#5C52C7] text-white text-xs font-bold shadow-button transition-all"
                >
                    <span wire:loading.remove wire:target="save">Save Task</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>

    </div>
</div>
