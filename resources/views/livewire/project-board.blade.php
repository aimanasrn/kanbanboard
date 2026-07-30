<div class="w-full h-full flex flex-col overflow-hidden bg-[#F8F5FF] dark:bg-[#12101F] transition-colors duration-300">
    
    <!-- REACTBITS INSPIRED FLOATING CARD NAVBAR (Top Island Header) -->
    <div class="mx-6 mt-4 p-2.5 rounded-[24px] card-nav-header flex items-center justify-between gap-4 z-30 shrink-0 select-none">
        
        <!-- Left: Logo & Floating Card Navigation Links -->
        <div class="flex items-center gap-6">
            <!-- Brand Logo Card -->
            <a href="{{ route('projects.index') }}" class="flex items-center gap-2.5 pl-2 group" title="All Projects">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#6E63D9] via-[#8675E6] to-[#E98AC9] p-0.5 shadow-button">
                    <div class="w-full h-full bg-[#6658C8] rounded-[14px] flex items-center justify-center text-white font-black text-lg">
                        K
                    </div>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-[#2F2F45] dark:text-white block leading-none group-hover:text-[#6E63D9] transition-colors">KanbanFlow</span>
                    <span class="text-[10px] font-semibold text-[#7A7A92] dark:text-[#A8A3C7]">← All Projects</span>
                </div>
            </a>

            <!-- Card Navigation Links (ReactBits Card Nav Tabs) -->
            <nav class="hidden lg:flex items-center gap-2">
                <button 
                    wire:click="setViewMode('kanban')" 
                    class="card-nav-item px-4 py-2 rounded-2xl font-bold text-xs flex items-center gap-2 transition-all {{ $viewMode === 'kanban' ? 'bg-[#6E63D9] text-white shadow-button' : 'bg-white dark:bg-[#25203D] text-[#2F2F45] dark:text-[#F2EEFF] border border-[#ECE8F7] dark:border-[#352F52] hover:bg-[#6E63D9]/10 hover:text-[#6E63D9]' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 01-2-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7m6 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>Kanban Board</span>
                </button>

                <button 
                    wire:click="setViewMode('timeline')" 
                    class="card-nav-item px-4 py-2 rounded-2xl font-bold text-xs flex items-center gap-2 transition-all {{ $viewMode === 'timeline' ? 'bg-[#6E63D9] text-white shadow-button' : 'bg-white dark:bg-[#25203D] text-[#2F2F45] dark:text-[#F2EEFF] border border-[#ECE8F7] dark:border-[#352F52] hover:bg-[#6E63D9]/10 hover:text-[#6E63D9]' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Schedule / Calendar</span>
                </button>

                {{-- Project Switcher Dropdown --}}
                @php $allProjects = \App\Models\Project::orderBy('name')->get(); @endphp
                @if($allProjects->count() > 1)
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open"
                                class="card-nav-item px-4 py-2 rounded-2xl font-bold text-xs flex items-center gap-2 transition-all bg-white dark:bg-[#25203D] text-[#2F2F45] dark:text-[#F2EEFF] border border-[#ECE8F7] dark:border-[#352F52] hover:bg-[#6E63D9]/10 hover:text-[#6E63D9]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                            <span>Switch Project</span>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute top-full left-0 mt-2 w-56 rounded-2xl bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-xl z-50 overflow-hidden py-1">
                            @foreach($allProjects as $proj)
                                <button wire:click="switchProject({{ $proj->id }})" @click="open = false"
                                        class="w-full text-left px-4 py-2.5 text-xs font-bold flex items-center gap-2.5 transition-colors
                                               {{ $proj->id === $projectId ? 'text-[#6E63D9] bg-[#6E63D9]/08' : 'text-[#2F2F45] dark:text-[#F2EEFF] hover:bg-[#F8F5FF] dark:hover:bg-[#25203D]' }}">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $proj->color ?? '#6E63D9' }};"></span>
                                    <span class="truncate">{{ $proj->name }}</span>
                                    @if($proj->id === $projectId)
                                        <svg class="w-3 h-3 ml-auto shrink-0 text-[#6E63D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>
        </div>

        <!-- Center: Search Input Pill -->
        <div class="relative flex-1 max-w-sm hidden md:block">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7A7A92]">
                <svg class="w-4 h-4 text-[#6E63D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input 
                type="text" 
                id="task-search-input"
                wire:model.live.debounce.250ms="search" 
                placeholder="Search tasks... (Press '/')"
                class="w-full pl-10 pr-4 py-2 rounded-full bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9] transition-all shadow-xs"
            />
        </div>

        <!-- Right: Actions, Theme Switcher & Profile Card -->
        <div class="flex items-center gap-2.5">
            <!-- AI Standup Button -->
            <button 
                wire:click="$toggle('showAiStandupModal')" 
                class="px-3.5 py-2 rounded-2xl bg-[#6E63D9]/10 hover:bg-[#6E63D9]/20 border border-[#6E63D9]/30 text-[#6E63D9] dark:text-[#A98BEF] font-extrabold text-xs flex items-center gap-1.5 transition-all shadow-xs"
                title="Generate AI Standup Summary"
            >
                <span class="text-sm">🤖</span>
                <span class="hidden xl:inline">AI Standup</span>
            </button>

            <!-- Visual Analytics Button -->
            <button 
                wire:click="$set('showAnalyticsModal', true)" 
                class="px-3.5 py-2 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9] text-[#2F2F45] dark:text-white font-extrabold text-xs flex items-center gap-1.5 transition-all shadow-xs"
                title="Open Visual Project Analytics"
            >
                <span class="text-sm">📊</span>
                <span class="hidden xl:inline">Analytics</span>
            </button>

            <!-- Team Members Button -->
            <button 
                wire:click="$set('showTeamModal', true)"
                class="px-3 py-1.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9] text-[#2F2F45] dark:text-white font-bold text-xs flex items-center gap-2 transition-all shadow-xs"
                title="Manage Project Team Members"
            >
                <div class="flex -space-x-1.5 overflow-hidden">
                    @foreach($project->members->take(3) as $m)
                        <img src="{{ $m->avatar_url }}" class="inline-block h-5 w-5 rounded-full ring-1 ring-white dark:ring-[#1C1830]" alt="">
                    @endforeach
                </div>
                <span>Team ({{ $project->members->count() + ($project->created_by && !$project->members->contains($project->created_by) ? 1 : 0) }})</span>
            </button>

            <!-- Keyboard Shortcuts Button -->
            <button 
                wire:click="$toggle('showShortcutsModal')" 
                class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white font-black text-xs transition-all shadow-xs"
                title="Keyboard Shortcuts (?)"
            >
                ?
            </button>

            <!-- Executive PDF Report Link -->
            <a 
                href="{{ route('project.report', $projectId) }}" 
                target="_blank" 
                class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all flex items-center gap-1.5 font-bold text-xs shadow-xs"
                title="Executive PDF Report"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="hidden xl:inline">PDF</span>
            </a>

            <!-- CSV Export Button -->
            <button 
                wire:click="exportTasksCsv" 
                class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all flex items-center gap-1.5 font-bold text-xs shadow-xs"
                title="Export Tasks to CSV"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="hidden xl:inline">CSV</span>
            </button>

            <!-- Notifications Bell -->
            <button wire:click="$toggle('showActivityDrawer')" class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all relative shadow-xs" title="Activity Log">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#FF6B81] ring-2 ring-white"></span>
            </button>

            <!-- Light/Dark Mode Switcher -->
            <button @click="toggleTheme()" class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#6E63D9] hover:bg-[#6E63D9] hover:text-white transition-all shadow-xs" title="Toggle Theme">
                <template x-if="darkMode">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </template>
                <template x-if="!darkMode">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </template>
            </button>

            <!-- Interactive Profile Card Pill (Click to open Profile Settings Modal) -->
            <div 
                wire:click="$set('showProfileModal', true)"
                class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9] cursor-pointer transition-all shadow-xs group"
                title="Edit Profile Settings"
            >
                <img src="{{ Auth::user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=User' }}" class="w-7 h-7 rounded-xl object-cover ring-2 ring-[#6E63D9]/20 group-hover:ring-[#6E63D9]" alt="">
                <span class="text-xs font-bold text-[#2F2F45] dark:text-white truncate max-w-[100px] group-hover:text-[#6E63D9]">{{ Auth::user()?->name ?? 'User' }}</span>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2.5 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#FF6B81] hover:bg-[#FF6B81] hover:text-white transition-all text-xs font-bold shadow-xs" title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>

            <!-- Primary New Task Button -->
            <button wire:click="openTaskModal(null, 'backlog')" class="px-5 py-2 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] hover:from-[#5C52C7] hover:to-[#7866DD] text-white font-bold text-xs shadow-button transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                <span>New Task</span>
                <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-bold bg-white/20 rounded-md text-white">N</kbd>
            </button>
        </div>
    </div>

    <!-- PROJECT HEADER & FILTER TOOLBAR -->
    <div class="px-8 pt-4 pb-3 flex items-center justify-between gap-4 shrink-0">
        <div>
            <h1 class="text-xl font-extrabold text-[#2F2F45] dark:text-white tracking-tight">{{ $project->name }}</h1>
            <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] mt-0.5 font-medium">
                {{ $stats['completion_rate'] }}% Completed • {{ $stats['total'] }} Tasks 
                @if(($stats['total_estimated_hours'] ?? 0) > 0 || ($stats['total_actual_hours'] ?? 0) > 0)
                    • ⏱️ <span class="font-bold text-[#6E63D9] dark:text-[#A98BEF]">{{ $stats['total_actual_hours'] ?? 0 }}h</span> / {{ $stats['total_estimated_hours'] ?? 0 }}h Spent
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3 overflow-x-auto custom-scrollbar py-1">
            <span class="text-xs font-bold text-[#7A7A92] dark:text-[#A8A3C7] shrink-0">Filter by:</span>

            <!-- Priority Filter Pill -->
            <div class="relative">
                <select wire:model.live="priorityFilter" class="appearance-none pl-4 pr-8 py-1.5 rounded-full bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] focus:outline-none cursor-pointer shadow-xs">
                    <option value="all">Priority: All</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <svg class="w-3.5 h-3.5 text-[#7A7A92] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </div>

            <!-- Assignee Filter Pill -->
            <div class="relative">
                <select wire:model.live="assigneeFilter" class="appearance-none pl-4 pr-8 py-1.5 rounded-full bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] focus:outline-none cursor-pointer shadow-xs">
                    <option value="all">Assignee: All</option>
                    <option value="unassigned">Unassigned</option>
                    @foreach($allUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <svg class="w-3.5 h-3.5 text-[#7A7A92] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </div>

            <!-- Due Date Filter Pill -->
            <div class="relative">
                <select wire:model.live="dueDateFilter" class="appearance-none pl-4 pr-8 py-1.5 rounded-full bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] focus:outline-none cursor-pointer shadow-xs">
                    <option value="all">Time: All</option>
                    <option value="overdue">Overdue</option>
                    <option value="today">Due Today</option>
                    <option value="this_week">Due This Week</option>
                </select>
                <svg class="w-3.5 h-3.5 text-[#7A7A92] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </div>

            <!-- Label Filter Pill -->
            <div class="relative">
                <select wire:model.live="labelFilter" class="appearance-none pl-4 pr-8 py-1.5 rounded-full bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-bold text-[#2F2F45] dark:text-[#F2EEFF] focus:outline-none cursor-pointer shadow-xs">
                    <option value="all">Tag: All</option>
                    @foreach($allLabels as $lbl)
                        <option value="{{ $lbl }}">#{{ $lbl }}</option>
                    @endforeach
                </select>
                <svg class="w-3.5 h-3.5 text-[#7A7A92] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </div>

            @if($search !== '' || $priorityFilter !== 'all' || $assigneeFilter !== 'all' || $dueDateFilter !== 'all' || $labelFilter !== 'all')
                <button wire:click="clearFilters" class="px-3.5 py-1.5 rounded-full bg-[#FF6B81]/15 hover:bg-[#FF6B81]/25 text-[#FF6B81] text-xs font-bold transition-all shrink-0">
                    Reset Filters
                </button>
            @endif
        </div>
    </div>

    <!-- MAIN VIEW AREA: KANBAN BOARD OR PROFESSIONAL CALENDAR -->
    @if($viewMode === 'kanban')
        <main class="flex-1 overflow-x-auto overflow-y-hidden px-8 pb-6 custom-scrollbar">
            @livewire('kanban-board', [
                'projectId' => $projectId,
                'search' => $search,
                'priorityFilter' => $priorityFilter,
                'assigneeFilter' => $assigneeFilter,
                'dueDateFilter' => $dueDateFilter,
                'labelFilter' => $labelFilter,
            ], key('kanban-grid-'.$projectId))
        </main>
    @else
        <!-- PROFESSIONAL SOFT UI CALENDAR & SCHEDULE VIEW -->
        <main class="flex-1 overflow-y-auto px-8 pb-6 custom-scrollbar space-y-4 relative">
            
            <!-- CALENDAR & SCHEDULE SKELETON LAZY LOADING OVERLAY -->
            <div wire:loading.delay.shortest wire:target="prevCalendarMonth, nextCalendarMonth, todayCalendarMonth, setViewMode, calendarSubView, search, priorityFilter, assigneeFilter, dueDateFilter, labelFilter, switchProject" class="absolute inset-x-8 inset-y-0 z-30 bg-white/90 dark:bg-[#1B182E]/90 backdrop-blur-xs rounded-[24px] p-6 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-[#ECE8F7] dark:border-[#2A2645]">
                    <div class="h-6 w-36 rounded-lg skeleton-shimmer"></div>
                    <div class="h-8 w-48 rounded-full skeleton-shimmer"></div>
                </div>
                <div class="grid grid-cols-7 gap-2">
                    @for($i=0; $i<35; $i++)
                        <div class="h-24 rounded-2xl border border-[#ECE8F7] dark:border-[#312C52] p-2 space-y-2">
                            <div class="h-3 w-6 rounded skeleton-shimmer"></div>
                            <div class="h-4 w-full rounded-xl skeleton-shimmer"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="bg-white dark:bg-[#1B182E] rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-6 shadow-soft-card space-y-6">
                
                <!-- Calendar Top Controls Bar -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-[#ECE8F7] dark:border-[#2A2645] pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-[#6E63D9]/10 text-[#6E63D9] font-bold">
                            📅
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[#2F2F45] dark:text-white tracking-tight">
                                {{ $currentMonthDate->format('F Y') }}
                            </h2>
                            <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7] font-medium">Click any date cell to add tasks or click event pills to edit.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Navigation Buttons -->
                        <div class="flex items-center gap-1 bg-[#F8F5FF] dark:bg-[#25203D] p-1 rounded-full border border-[#ECE8F7] dark:border-[#352F52]">
                            <button wire:click="prevCalendarMonth" class="p-1.5 rounded-full hover:bg-white dark:hover:bg-slate-700 text-[#6E63D9] transition-all" title="Previous Month">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            <button wire:click="todayCalendarMonth" class="px-3 py-1 rounded-full text-xs font-extrabold text-[#6E63D9] hover:bg-white dark:hover:bg-slate-700 transition-all">
                                Today
                            </button>

                            <button wire:click="nextCalendarMonth" class="p-1.5 rounded-full hover:bg-white dark:hover:bg-slate-700 text-[#6E63D9] transition-all" title="Next Month">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        <!-- View Sub-tabs (Month Grid vs Task List) -->
                        <div class="flex items-center gap-1 bg-[#F8F5FF] dark:bg-[#25203D] p-1 rounded-full border border-[#ECE8F7] dark:border-[#352F52]">
                            <button wire:click="$set('calendarSubView', 'month')" class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $calendarSubView === 'month' ? 'bg-[#6E63D9] text-white shadow-button' : 'text-[#7A7A92] hover:text-[#2F2F45]' }}">
                                Month Grid
                            </button>
                            <button wire:click="$set('calendarSubView', 'list')" class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $calendarSubView === 'list' ? 'bg-[#6E63D9] text-white shadow-button' : 'text-[#7A7A92] hover:text-[#2F2F45]' }}">
                                Schedule List
                            </button>
                        </div>
                    </div>
                </div>

                @if($calendarSubView === 'month')
                    <!-- MONTH GRID CALENDAR -->
                    <div class="space-y-2">
                        <!-- Days of Week Header -->
                        <div class="grid grid-cols-7 gap-2 text-center text-xs font-extrabold text-[#7A7A92] dark:text-[#A8A3C7] py-2 border-b border-[#ECE8F7] dark:border-[#2A2645]">
                            <div>MON</div>
                            <div>TUE</div>
                            <div>WED</div>
                            <div>THU</div>
                            <div>FRI</div>
                            <div class="text-[#6E63D9]">SAT</div>
                            <div class="text-[#6E63D9]">SUN</div>
                        </div>

                        <!-- 35/42 Date Grid Cells -->
                        <div class="grid grid-cols-7 gap-2">
                            @foreach($calendarDays as $cDay)
                                @php
                                    $dayTasks = $tasksByDate[$cDay['date']] ?? [];
                                @endphp
                                <div 
                                    class="min-h-[110px] p-2 rounded-2xl border transition-all duration-200 flex flex-col justify-between {{ $cDay['is_current_month'] ? 'bg-white dark:bg-[#221F3B] border-[#ECE8F7] dark:border-[#312C52] shadow-xs' : 'bg-[#F8F5FF]/50 dark:bg-[#151326]/50 border-transparent opacity-40' }} {{ $cDay['is_today'] ? 'ring-2 ring-[#6E63D9] shadow-soft-card' : '' }}"
                                >
                                    <!-- Cell Header: Date Number & Add Shortcut -->
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-black {{ $cDay['is_today'] ? 'w-6 h-6 rounded-full bg-[#6E63D9] text-white flex items-center justify-center' : ($cDay['is_current_month'] ? 'text-[#2F2F45] dark:text-white' : 'text-[#7A7A92]') }}">
                                            {{ $cDay['day_number'] }}
                                        </span>

                                        @if($cDay['is_current_month'])
                                            <button 
                                                wire:click="openTaskModal(null, 'todo')" 
                                                class="w-5 h-5 rounded-full hover:bg-[#6E63D9]/10 text-[#7A7A92] hover:text-[#6E63D9] flex items-center justify-center text-xs font-bold transition-all opacity-0 group-hover:opacity-100"
                                                title="Add task on {{ $cDay['date'] }}"
                                            >
                                                +
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Cell Event Items List -->
                                    <div class="flex-1 space-y-1.5 overflow-y-auto custom-scrollbar max-h-[85px]">
                                        @foreach($dayTasks as $t)
                                            @php
                                                $chipColor = match($t->priority) {
                                                    'urgent' => 'bg-[#FF6B81]/15 text-[#FF6B81] border-[#FF6B81]/30',
                                                    'high' => 'bg-amber-500/15 text-amber-600 border-amber-500/30',
                                                    'medium' => 'bg-blue-500/15 text-blue-600 border-blue-500/30',
                                                    'low' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 border-slate-200',
                                                    default => 'bg-[#6E63D9]/10 text-[#6E63D9]',
                                                };
                                            @endphp
                                            <div 
                                                wire:click="openTaskModal({{ $t->id }})"
                                                wire:loading.class="animate-pulse opacity-50"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold border truncate flex items-center justify-between cursor-pointer hover:scale-102 transition-all shadow-2xs {{ $chipColor }}"
                                                title="{{ $t->title }}"
                                            >
                                                <span class="truncate">{{ $t->title }}</span>
                                                @if($t->assignee)
                                                    <img src="{{ $t->assignee->avatar_url }}" class="w-4 h-4 rounded-full object-cover shrink-0" alt="">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- SCHEDULE LIST VIEW -->
                    <div class="space-y-3">
                        @forelse($project->tasks->sortBy('due_date') as $t)
                            @php
                                $statusColor = match($t->status) {
                                    'backlog' => 'bg-[#6E63D9]/15 text-[#6E63D9]',
                                    'todo' => 'bg-blue-500/15 text-blue-600',
                                    'in_progress' => 'bg-amber-500/15 text-amber-600',
                                    'review' => 'bg-[#A98BEF]/20 text-[#6E63D9]',
                                    'done' => 'bg-[#72D49A]/20 text-[#2AA857]',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <div 
                                wire:click="openTaskModal({{ $t->id }})" 
                                wire:loading.class="animate-pulse opacity-50"
                                class="flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] hover:border-[#6E63D9] hover:shadow-card-hover transition-all duration-200 cursor-pointer shadow-xs"
                            >
                                <div class="flex items-center gap-4 flex-1 min-w-0 pr-4">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold shrink-0 {{ $statusColor }}">
                                        {{ strtoupper(str_replace('_', ' ', $t->status)) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white truncate">{{ $t->title }}</h4>
                                        <p class="text-[11px] text-[#7A7A92] dark:text-[#A8A3C7] truncate mt-0.5">{{ $t->description ?: 'No detailed description' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 shrink-0">
                                    @if($t->estimated_hours || $t->actual_hours)
                                        <div class="flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-[#6E63D9]/10 text-[#6E63D9]">
                                            ⏱️ {{ $t->actual_hours ?? 0 }}h / {{ $t->estimated_hours ?? 0 }}h
                                        </div>
                                    @endif

                                    <x-priority-badge :priority="$t->priority" />

                                    <div class="text-right text-xs">
                                        <span class="font-bold text-[#2F2F45] dark:text-white block">
                                            {{ $t->due_date ? $t->due_date->format('M d, Y') : 'No due date' }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-[#7A7A92] dark:text-[#A8A3C7]">
                                            {{ $t->due_date ? $t->due_date->diffForHumans() : '-' }}
                                        </span>
                                    </div>

                                    @if($t->assignee)
                                        <img src="{{ $t->assignee->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-[#6E63D9]/20" title="{{ $t->assignee->name }}" alt="">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-[#F8F5FF] dark:bg-slate-800 border border-dashed border-[#A98BEF] flex items-center justify-center text-[10px] text-[#6E63D9] font-bold">?</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-[#7A7A92] py-10 font-medium">No tasks found in project schedule.</p>
                        @endforelse
                    </div>
                @endif

            </div>
        </main>
    @endif

    <!-- USER PROFILE SETTINGS MODAL -->
    @if($showProfileModal)
        @livewire('profile-modal', key('profile-modal-'.Auth::id()))
    @endif

    <!-- AI DAILY STANDUP MODAL -->
    @if($showAiStandupModal && $aiSummary)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#12101F]/60 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-[#1B182E] max-w-lg w-full rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-[#ECE8F7] dark:border-[#2A2645] pb-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 text-[#6E63D9] text-xl">
                            🤖
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">AI Standup Summary</h3>
                            <p class="text-xs text-[#7A7A92]">Automated health check & bottleneck advisor.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showAiStandupModal', false)" class="p-1.5 rounded-full text-[#7A7A92] hover:bg-[#ECE8F7]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Health Score Progress -->
                <div class="bg-[#F8F5FF] dark:bg-[#25203D] p-4 rounded-2xl border border-[#ECE8F7] dark:border-[#352F52] space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-[#2F2F45] dark:text-white">Project Sprint Health</span>
                        <span class="text-[#6E63D9] font-extrabold text-sm">{{ $aiSummary['health_score'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-[#ECE8F7] dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#6E63D9] to-[#72D49A] rounded-full transition-all" style="width: {{ $aiSummary['health_score'] }}%"></div>
                    </div>
                </div>

                <!-- Bottlenecks Alert -->
                @if(count($aiSummary['bottlenecks']) > 0)
                    <div class="p-3.5 rounded-2xl bg-[#FF6B81]/10 border border-[#FF6B81]/30 text-xs text-[#FF6B81] space-y-1">
                        <span class="font-extrabold block">⚠️ Identified Bottlenecks:</span>
                        <ul class="list-disc list-inside space-y-0.5 font-semibold text-[11px]">
                            @foreach($aiSummary['bottlenecks'] as $b)
                                <li>{{ $b }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Recommendation Box -->
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-700 dark:text-emerald-300 space-y-1">
                    <span class="font-extrabold block">💡 AI Recommendation:</span>
                    <p class="font-medium text-[11px] leading-relaxed">{{ $aiSummary['recommendation'] }}</p>
                </div>

                <button wire:click="$set('showAiStandupModal', false)" class="w-full py-2.5 rounded-full bg-[#6E63D9] text-white font-bold text-xs shadow-button hover:bg-[#5C52C7] transition-all">
                    Dismiss Summary
                </button>
            </div>
        </div>
    @endif

    <!-- KEYBOARD SHORTCUTS MODAL -->
    @if($showShortcutsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#12101F]/60 backdrop-blur-md animate-fadeIn" @click.outside="showShortcutsModal = false">
            <div class="bg-white dark:bg-[#1B182E] max-w-md w-full rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#ECE8F7] dark:border-[#2A2645] pb-3">
                    <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">Keyboard Shortcuts</h3>
                    <button wire:click="$set('showShortcutsModal', false)" class="p-1.5 rounded-full text-[#7A7A92] hover:bg-[#ECE8F7]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D]">
                        <span class="font-bold text-[#2F2F45] dark:text-white">Create New Task</span>
                        <kbd class="px-2 py-1 rounded bg-white dark:bg-[#1B182E] font-black text-[#6E63D9] border border-[#ECE8F7]">N</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D]">
                        <span class="font-bold text-[#2F2F45] dark:text-white">Focus Search Bar</span>
                        <kbd class="px-2 py-1 rounded bg-white dark:bg-[#1B182E] font-black text-[#6E63D9] border border-[#ECE8F7]">/</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D]">
                        <span class="font-bold text-[#2F2F45] dark:text-white">Toggle Keyboard Shortcuts</span>
                        <kbd class="px-2 py-1 rounded bg-white dark:bg-[#1B182E] font-black text-[#6E63D9] border border-[#ECE8F7]">?</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F8F5FF] dark:bg-[#25203D]">
                        <span class="font-bold text-[#2F2F45] dark:text-white">Close Open Modals</span>
                        <kbd class="px-2.5 py-1 rounded bg-white dark:bg-[#1B182E] font-black text-[#6E63D9] border border-[#ECE8F7]">Esc</kbd>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TASK MODAL -->
    @if($showTaskModal)
        @livewire('task-modal', [
            'taskId' => $editingTaskId,
            'defaultStatus' => $defaultColumnStatus,
            'projectId' => $projectId,
        ], key('task-modal-'.($editingTaskId ?? 'new')))
    @endif

    <!-- DELETE TASK CONFIRMATION MODAL -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#12101F]/60 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-[#1B182E] max-w-sm w-full rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-6 shadow-2xl text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-[#FF6B81]/15 text-[#FF6B81] flex items-center justify-center mx-auto text-xl font-black">
                    🗑️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">Delete Task?</h3>
                    <p class="text-xs text-[#7A7A92] mt-1 font-medium">This action cannot be undone. Task comments & attachments will also be permanently deleted.</p>
                </div>
                <div class="flex justify-center gap-3 pt-2">
                    <button wire:click="$set('showDeleteModal', false)" class="px-5 py-2 rounded-full bg-[#ECE8F7] dark:bg-[#25203D] text-[#7A7A92] font-bold text-xs hover:bg-[#DDD8F0]">
                        Cancel
                    </button>
                    <button wire:click="deleteTaskConfirmed" class="px-5 py-2 rounded-full bg-[#FF6B81] text-white font-bold text-xs shadow-button hover:bg-[#E5576D]">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ACTIVITY LOG DRAWER -->
    <div 
        x-data 
        x-show="$wire.showActivityDrawer" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-96 bg-white dark:bg-[#1B182E] border-l border-[#ECE8F7] dark:border-[#2A2645] shadow-2xl flex flex-col"
    >
        <div class="p-6 border-b border-[#ECE8F7] dark:border-[#2A2645] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">🔔</span>
                <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">Workspace Audit Trail</h3>
            </div>
            <button wire:click="$set('showActivityDrawer', false)" class="p-1.5 rounded-full text-[#7A7A92] hover:bg-[#ECE8F7]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
            @forelse($recentActivities as $act)
                <div class="flex items-start gap-3 p-3 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52]">
                    <img src="{{ $act->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=User' }}" class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-[#6E63D9]/20" alt="">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-[#2F2F45] dark:text-white leading-snug">
                            <span class="text-[#6E63D9]">{{ $act->user?->name ?? 'System' }}</span> {{ $act->description }}
                        </p>
                        <span class="text-[10px] text-[#7A7A92] font-semibold mt-1 block">
                            {{ $act->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-center text-xs text-[#7A7A92] py-10 font-medium">No recent activity recorded.</p>
            @endforelse
        </div>
    </div>

    <!-- TOAST MESSAGES -->
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
            class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-xs font-bold max-w-sm
                {{ $toastMessage['type'] === 'success' ? 'bg-[#72D49A]' : ($toastMessage['type'] === 'error' ? 'bg-[#FF6B81]' : 'bg-[#6E63D9]') }}"
            wire:key="toast-{{ $toastMessage['id'] ?? 0 }}"
        >
            <span class="text-base">
                {{ $toastMessage['type'] === 'success' ? '✅' : ($toastMessage['type'] === 'error' ? '⚠️' : 'ℹ️') }}
            </span>
            <span>{{ $toastMessage['message'] }}</span>
        </div>
    @endif

    <!-- MANAGE PROJECT TEAM MEMBERS MODAL -->
    @if($showTeamModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(18,16,31,0.6); backdrop-filter: blur(8px);"
        >
            <div class="w-full max-w-md rounded-[24px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#ECE8F7] dark:border-[#2A2545] pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-[#6E63D9]/10 text-[#6E63D9] flex items-center justify-center text-lg font-bold">
                            👥
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#2F2F45] dark:text-white">Manage Project Team</h3>
                            <p class="text-xs text-[#7A7A92]">Project: <span class="font-bold text-[#6E63D9]">{{ $project->name }}</span></p>
                        </div>
                    </div>
                    <button wire:click="$set('showTeamModal', false)" class="p-1.5 rounded-full text-[#7A7A92] hover:bg-[#ECE8F7]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto custom-scrollbar pr-1">
                    @foreach($allUsers as $u)
                        @php
                            $isOwner = $project->created_by === $u->id;
                            $isMem = $project->isMember($u->id);
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52]">
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-[#6E63D9]/20" alt="">
                                <div>
                                    <h4 class="text-xs font-bold text-[#2F2F45] dark:text-white flex items-center gap-1.5">
                                        <span>{{ $u->name }}</span>
                                        @if($isOwner)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-[#6E63D9]/15 text-[#6E63D9]">Owner</span>
                                        @endif
                                    </h4>
                                    <p class="text-[10px] text-[#7A7A92]">{{ $u->email }}</p>
                                </div>
                            </div>

                            @if(!$isOwner)
                                <button 
                                    wire:click="toggleProjectMember({{ $u->id }})"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all {{ $isMem ? 'bg-rose-50 text-[#FF6B81] hover:bg-rose-100' : 'bg-[#6E63D9] text-white hover:bg-[#5C52C7]' }}"
                                >
                                    {{ $isMem ? 'Remove' : '+ Add' }}
                                </button>
                            @else
                                <span class="text-xs font-extrabold text-[#7A7A92] px-3 py-1">Owner</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 border-t border-[#ECE8F7] dark:border-[#2A2545] flex justify-end">
                    <button wire:click="$set('showTeamModal', false)" class="px-5 py-2 rounded-full bg-[#6E63D9] text-white text-xs font-bold shadow-button">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- VISUAL PROJECT ANALYTICS MODAL -->
    @if($showAnalyticsModal)
        @php
            $analytics = $this->getAnalyticsData();
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="background: rgba(18,16,31,0.65); backdrop-filter: blur(8px);"
            x-data="{
                initCharts() {
                    this.$nextTick(() => {
                        // Status Doughnut Chart
                        const statusCtx = document.getElementById('statusChart');
                        if (statusCtx) {
                            new Chart(statusCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: Object.keys({{ json_encode($analytics['status']) }}),
                                    datasets: [{
                                        data: Object.values({{ json_encode($analytics['status']) }}),
                                        backgroundColor: ['#6E63D9', '#3B82F6', '#FFC857', '#A98BEF', '#72D49A'],
                                        borderWidth: 0,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } } }
                                }
                            });
                        }

                        // Priority Bar Chart
                        const prioCtx = document.getElementById('priorityChart');
                        if (prioCtx) {
                            new Chart(prioCtx, {
                                type: 'bar',
                                data: {
                                    labels: Object.keys({{ json_encode($analytics['priority']) }}),
                                    datasets: [{
                                        label: 'Tasks',
                                        data: Object.values({{ json_encode($analytics['priority']) }}),
                                        backgroundColor: ['#FF6B81', '#F59E0B', '#3B82F6', '#94A3B8'],
                                        borderRadius: 8,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                                }
                            });
                        }
                    });
                }
            }"
            x-init="initCharts()"
        >
            <!-- Script Chart.js CDN -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <div class="w-full max-w-4xl rounded-[28px] bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] shadow-2xl p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-[#ECE8F7] dark:border-[#2A2545] pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#6E63D9] to-[#8675E6] text-white flex items-center justify-center text-xl font-bold shadow-button">
                            📊
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[#2F2F45] dark:text-white tracking-tight">Project Analytics & Workload</h2>
                            <p class="text-xs text-[#7A7A92] font-medium">Real-time metrics, task velocity, and team workload distribution.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showAnalyticsModal', false)" class="p-2 rounded-full text-[#7A7A92] hover:bg-[#ECE8F7] dark:hover:bg-[#25203D]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Top Metric Cards Row -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52]">
                        <span class="text-[11px] font-bold text-[#7A7A92] block mb-1">Total Active Tasks</span>
                        <span class="text-xl font-black text-[#2F2F45] dark:text-white">{{ array_sum($analytics['status']) }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#72D49A]/10 border border-[#72D49A]/30">
                        <span class="text-[11px] font-bold text-emerald-600 block mb-1">Completed Tasks</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $analytics['status']['Done'] ?? 0 }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#6E63D9]/10 border border-[#6E63D9]/30">
                        <span class="text-[11px] font-bold text-[#6E63D9] block mb-1">Total Estimated</span>
                        <span class="text-xl font-black text-[#6E63D9] dark:text-[#A98BEF]">{{ $analytics['total_estimated'] }} hrs</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30">
                        <span class="text-[11px] font-bold text-amber-600 block mb-1">Total Spent / Logged</span>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $analytics['total_actual'] }} hrs</span>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Distribution Doughnut Chart -->
                    <div class="p-5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] space-y-3">
                        <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white flex items-center gap-2">
                            <span>📌</span> Status Breakdown
                        </h4>
                        <div class="h-56 relative">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>

                    <!-- Priority Distribution Bar Chart -->
                    <div class="p-5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] space-y-3">
                        <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white flex items-center gap-2">
                            <span>🚨</span> Priority Distribution
                        </h4>
                        <div class="h-56 relative">
                            <canvas id="priorityChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Team Workload Distribution Table -->
                <div class="p-5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] space-y-3">
                    <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white flex items-center gap-2">
                        <span>👥</span> Team Workload & Logged Hours
                    </h4>
                    <div class="space-y-2">
                        @foreach($analytics['assignees'] as $name => $data)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-[#1C1830] border border-[#ECE8F7] dark:border-[#2A2545] text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#6E63D9]/10 text-[#6E63D9] font-black flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($name, 0, 1)) }}
                                    </div>
                                    <span class="font-extrabold text-[#2F2F45] dark:text-white">{{ $name }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-xs font-bold">
                                    <span class="px-2.5 py-1 rounded-full bg-[#6E63D9]/10 text-[#6E63D9]">{{ $data['tasks'] }} Tasks</span>
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600">⏱️ {{ round($data['hours'], 1) }} hrs</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-[#ECE8F7] dark:border-[#2A2545] flex justify-end">
                    <button wire:click="$set('showAnalyticsModal', false)" class="px-6 py-2.5 rounded-full bg-[#6E63D9] text-white font-bold text-xs shadow-button hover:bg-[#5C52C7] transition-all">
                        Close Analytics
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

