<div class="w-full h-full flex flex-col overflow-hidden bg-[#F8F5FF] dark:bg-[#12101F] transition-colors duration-300">

    {{-- TOP NAVBAR --}}
    <div class="mx-6 mt-4 p-2.5 rounded-[24px] flex items-center justify-between gap-4 z-30 shrink-0 select-none"
         style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); border: 1px solid rgba(110,99,217,0.12); box-shadow: 0 4px 24px rgba(110,99,217,0.08);">

        {{-- Brand --}}
        <div class="flex items-center gap-2.5 pl-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#6E63D9] via-[#8675E6] to-[#E98AC9] p-0.5 shadow-lg">
                    <div class="w-full h-full bg-[#6658C8] rounded-[14px] flex items-center justify-center text-white font-black text-lg">K</div>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-[#2F2F45] dark:text-white block leading-none">KanbanFlow</span>
                    <span class="text-[10px] font-semibold text-[#7A7A92] dark:text-[#A8A3C7]">Project Workspace</span>
                </div>
            </a>
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2.5">
            {{-- Dark mode --}}
            <button @click="toggleTheme()"
                    class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all shadow-xs"
                    title="Toggle Theme">
                <template x-if="darkMode">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </template>
                <template x-if="!darkMode">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </template>
            </button>

            {{-- Profile --}}
            <div class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] shadow-xs">
                <img src="{{ Auth::user()?->avatar_url }}" class="w-7 h-7 rounded-xl object-cover ring-2 ring-[#6E63D9]/20" alt="">
                <span class="text-xs font-bold text-[#2F2F45] dark:text-white truncate max-w-[100px]">{{ Auth::user()?->name }}</span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#FF6B81] hover:bg-[#FF6B81] hover:text-white transition-all text-xs font-bold shadow-xs"
                        title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>

            {{-- New Project Button --}}
            <button wire:click="openCreateModal"
                    class="px-5 py-2 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] hover:from-[#5C52C7] hover:to-[#7866DD] text-white font-bold text-xs shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                <span>New Project</span>
            </button>
        </div>
    </div>

    {{-- MAIN SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto px-8 py-6 custom-scrollbar">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-[#2F2F45] dark:text-white tracking-tight">All Projects</h1>
            <p class="text-sm text-[#7A7A92] dark:text-[#A8A3C7] mt-1">{{ $projects->count() }} project{{ $projects->count() !== 1 ? 's' : '' }} in your workspace</p>
        </div>

        {{-- Projects Grid --}}
        @if($projects->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#6E63D9]/20 to-[#E98AC9]/20 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-[#6E63D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                </div>
                <h3 class="text-lg font-extrabold text-[#2F2F45] dark:text-white mb-2">No projects yet</h3>
                <p class="text-sm text-[#7A7A92] dark:text-[#A8A3C7] mb-6 max-w-xs">Create your first project to start organising tasks in a beautiful Kanban board.</p>
                <button wire:click="openCreateModal"
                        class="px-6 py-2.5 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] text-white font-bold text-sm shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    Create First Project
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($projects as $project)
                    @php
                        $stats = $project->stats;
                        $color = $project->color ?? '#6E63D9';
                    @endphp
                    <div class="group relative flex flex-col rounded-[20px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden cursor-pointer"
                         wire:click="goToProject({{ $project->id }})">

                        {{-- Colored top strip --}}
                        <div class="h-1.5 w-full" style="background: {{ $color }};"></div>

                        {{-- Card Body --}}
                        <div class="flex flex-col flex-1 p-5">

                            {{-- Project icon + title --}}
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 text-white font-black text-base shadow-md"
                                         style="background: {{ $color }};">
                                        {{ strtoupper(substr($project->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-extrabold text-[#2F2F45] dark:text-white truncate leading-tight">{{ $project->name }}</h3>
                                        @if($project->owner)
                                            <span class="text-[10px] text-[#7A7A92] dark:text-[#A8A3C7] font-medium">by {{ $project->owner->name }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Owner actions: edit/delete --}}
                                @if($project->is_owner)
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
                                         x-data @click.stop="">
                                        <button wire:click.stop="openEditModal({{ $project->id }})"
                                                class="p-1.5 rounded-xl hover:bg-[#6E63D9]/10 text-[#7A7A92] hover:text-[#6E63D9] transition-all"
                                                title="Edit project">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click.stop="confirmDeleteProject({{ $project->id }})"
                                                class="p-1.5 rounded-xl hover:bg-[#FF6B81]/10 text-[#7A7A92] hover:text-[#FF6B81] transition-all"
                                                title="Delete project">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Description --}}
                            @if($project->description)
                                <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] mb-4 line-clamp-2 leading-relaxed">{{ $project->description }}</p>
                            @else
                                <p class="text-xs text-[#7A7A92]/50 dark:text-[#A8A3C7]/40 mb-4 italic">No description</p>
                            @endif

                            {{-- Spacer --}}
                            <div class="flex-1"></div>

                            {{-- Progress bar --}}
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-bold text-[#7A7A92] dark:text-[#A8A3C7]">Progress</span>
                                    <span class="text-[10px] font-extrabold" style="color: {{ $color }};">{{ $stats['completion_pct'] }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-[#ECE8F7] dark:bg-[#2A2545] rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $stats['completion_pct'] }}%; background: {{ $color }};"></div>
                                </div>
                            </div>

                            {{-- Stats row --}}
                            <div class="flex items-center gap-3 pt-3 border-t border-[#ECE8F7] dark:border-[#2A2545]">
                                <div class="flex items-center gap-1 text-[10px] font-bold text-[#7A7A92] dark:text-[#A8A3C7]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    {{ $stats['total'] }} tasks
                                </div>
                                <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $stats['done'] }} done
                                </div>
                                @if($stats['overdue'] > 0)
                                    <div class="flex items-center gap-1 text-[10px] font-bold text-[#FF6B81]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $stats['overdue'] }} overdue
                                    </div>
                                @endif
                                @if($stats['in_progress'] > 0)
                                    <div class="flex items-center gap-1 text-[10px] font-bold text-amber-500">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
                                        {{ $stats['in_progress'] }} active
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Open arrow indicator on hover --}}
                        <div class="absolute bottom-4 right-4 w-7 h-7 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-2"
                             style="background: {{ $color }}20; color: {{ $color }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                @endforeach

                {{-- Create New Project Card --}}
                <div wire:click="openCreateModal"
                     class="flex flex-col items-center justify-center rounded-[20px] border-2 border-dashed border-[#6E63D9]/30 hover:border-[#6E63D9] bg-[#6E63D9]/5 hover:bg-[#6E63D9]/10 transition-all cursor-pointer group min-h-[200px]">
                    <div class="w-12 h-12 rounded-2xl bg-[#6E63D9]/15 group-hover:bg-[#6E63D9] flex items-center justify-center mb-3 transition-all">
                        <svg class="w-6 h-6 text-[#6E63D9] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-sm font-bold text-[#6E63D9] group-hover:text-[#5C52C7]">New Project</span>
                    <span class="text-[10px] text-[#7A7A92] mt-1">Click to create</span>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== CREATE / EDIT PROJECT MODAL ===== --}}
    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.animation='fadeIn 0.15s ease'"
             style="background: rgba(18,16,31,0.6); backdrop-filter: blur(8px);">
            <div class="w-full max-w-md rounded-[24px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl overflow-hidden"
                 @click.stop="">

                {{-- Modal Header --}}
                <div class="px-6 pt-6 pb-4 border-b border-[#ECE8F7] dark:border-[#2A2545] flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">
                            {{ $editingProjectId ? 'Edit Project' : 'Create New Project' }}
                        </h3>
                        <p class="text-xs text-[#7A7A92] mt-0.5">
                            {{ $editingProjectId ? 'Update project details' : 'Start a new project workspace' }}
                        </p>
                    </div>
                    <button wire:click="$set('showProjectModal', false)"
                            class="p-2 rounded-xl hover:bg-[#ECE8F7] dark:hover:bg-[#2A2545] text-[#7A7A92] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-5">

                    {{-- Project Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] mb-1.5">Project Name <span class="text-[#FF6B81]">*</span></label>
                        <input type="text"
                               id="project-name-input"
                               wire:model="projectName"
                               placeholder="e.g. Website Redesign, Mobile App..."
                               autofocus
                               class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-sm font-semibold text-[#2F2F45] dark:text-white placeholder-[#A8A3C7] focus:outline-none focus:border-[#6E63D9] transition-all">
                        @error('projectName')
                            <p class="text-xs text-[#FF6B81] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] mb-1.5">Description</label>
                        <textarea wire:model="projectDescription"
                                  rows="3"
                                  placeholder="What is this project about?"
                                  class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-sm text-[#2F2F45] dark:text-white placeholder-[#A8A3C7] focus:outline-none focus:border-[#6E63D9] transition-all resize-none"></textarea>
                    </div>

                    {{-- Color Picker --}}
                    <div>
                        <label class="block text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] mb-2">Project Color</label>
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($colorOptions as $c)
                                <button type="button"
                                        wire:click="$set('projectColor', '{{ $c }}')"
                                        class="w-7 h-7 rounded-full transition-all transform hover:scale-110 {{ $projectColor === $c ? 'ring-2 ring-offset-2 ring-[#6E63D9] scale-110' : '' }}"
                                        style="background: {{ $c }};">
                                </button>
                            @endforeach
                            {{-- Custom color --}}
                            <div class="relative">
                                <input type="color"
                                       wire:model.live="projectColor"
                                       class="w-7 h-7 rounded-full cursor-pointer border-2 border-[#ECE8F7] overflow-hidden"
                                       title="Custom color">
                            </div>
                        </div>
                        {{-- Preview --}}
                        <div class="mt-2 flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full" style="background: {{ $projectColor }};"></div>
                            <span class="text-xs text-[#7A7A92]">{{ $projectColor }}</span>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 pb-6 flex items-center gap-3 justify-end">
                    <button wire:click="$set('showProjectModal', false)"
                            class="px-5 py-2 rounded-full bg-[#ECE8F7] dark:bg-[#25203D] text-[#7A7A92] dark:text-[#A8A3C7] font-bold text-xs hover:bg-[#DDD8F0] transition-all">
                        Cancel
                    </button>
                    <button wire:click="saveProject"
                            class="px-6 py-2 rounded-full text-white font-bold text-xs shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center gap-2"
                            style="background: linear-gradient(135deg, {{ $projectColor }}, {{ $projectColor }}dd);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $editingProjectId ? 'Save Changes' : 'Create Project' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== DELETE CONFIRMATION MODAL ===== --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background: rgba(18,16,31,0.6); backdrop-filter: blur(8px);">
            <div class="w-full max-w-sm rounded-[24px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-[#FF6B81]/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-[#FF6B81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white mb-2">Delete Project?</h3>
                <p class="text-sm text-[#7A7A92] dark:text-[#A8A3C7] mb-1">
                    You are about to permanently delete
                </p>
                <p class="text-sm font-bold text-[#FF6B81] mb-4">"{{ $projectToDeleteName }}"</p>
                <p class="text-xs text-[#7A7A92] mb-6">All tasks, comments, and attachments will be deleted. This action cannot be undone.</p>
                <div class="flex items-center gap-3 justify-center">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="px-5 py-2 rounded-full bg-[#ECE8F7] dark:bg-[#25203D] text-[#7A7A92] font-bold text-xs hover:bg-[#DDD8F0] transition-all">
                        Cancel
                    </button>
                    <button wire:click="deleteProjectConfirmed"
                            class="px-5 py-2 rounded-full bg-gradient-to-r from-[#FF6B81] to-[#FF4560] text-white font-bold text-xs shadow-lg hover:shadow-xl transition-all">
                        Delete Project
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== TOAST ===== --}}
    @if(!empty($toastMessage))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3500)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-bold max-w-sm
                {{ $toastMessage['type'] === 'success' ? 'bg-emerald-500' : ($toastMessage['type'] === 'error' ? 'bg-[#FF6B81]' : 'bg-[#6E63D9]') }}"
            wire:key="toast-{{ $toastMessage['id'] ?? 0 }}">
            @if($toastMessage['type'] === 'success')
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            @elseif($toastMessage['type'] === 'error')
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
            {{ $toastMessage['message'] }}
        </div>
    @endif

</div>
