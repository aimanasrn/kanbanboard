<div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-[#12101F]/60 backdrop-blur-md animate-fadeIn" @keydown.escape.window="$wire.dispatch('close-profile-modal')">
    <div class="bg-white dark:bg-[#1B182E] max-w-md w-full rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] shadow-2xl overflow-hidden flex flex-col space-y-5 p-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-[#ECE8F7] dark:border-[#2A2645] pb-3">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-[#6E63D9]/10 text-[#6E63D9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-[#2F2F45] dark:text-white">Profile Settings</h2>
                    <p class="text-xs text-[#7A7A92] dark:text-[#A8A3C7]">Manage your personal information & password.</p>
                </div>
            </div>
            <button wire:click="$dispatch('close-profile-modal')" class="p-1 text-[#7A7A92] hover:text-[#2F2F45]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Avatar Preview Card -->
        <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52]">
            <img src="{{ !empty($avatar) ? $avatar : Auth::user()?->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-[#6E63D9]/30" alt="">
            <div class="flex-1 min-w-0">
                <h4 class="text-xs font-extrabold text-[#2F2F45] dark:text-white truncate">{{ $name ?: 'Your Name' }}</h4>
                <p class="text-[10px] text-[#7A7A92] dark:text-[#A8A3C7] font-semibold truncate">{{ $role ?: 'Role' }}</p>
            </div>
        </div>

        <!-- Settings Form -->
        <form wire:submit.prevent="saveProfile" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="block font-bold text-[#2F2F45] dark:text-slate-300">Full Name</label>
                <input 
                    type="text" 
                    wire:model="name" 
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white font-semibold focus:outline-none focus:border-[#6E63D9]"
                />
                @error('name') <span class="text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#2F2F45] dark:text-slate-300">Email Address</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white font-semibold focus:outline-none focus:border-[#6E63D9]"
                />
                @error('email') <span class="text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#2F2F45] dark:text-slate-300">Job Title / Role</label>
                <input 
                    type="text" 
                    wire:model="role" 
                    placeholder="e.g. Lead UI/UX Designer"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white font-semibold focus:outline-none focus:border-[#6E63D9]"
                />
                @error('role') <span class="text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#2F2F45] dark:text-slate-300">Avatar Image URL (Optional)</label>
                <input 
                    type="text" 
                    wire:model.live="avatar" 
                    placeholder="https://images.unsplash.com/..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white font-semibold focus:outline-none focus:border-[#6E63D9]"
                />
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-[#2F2F45] dark:text-slate-300">New Password (Optional)</label>
                <input 
                    type="password" 
                    wire:model="new_password" 
                    placeholder="Leave blank to keep current"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white font-semibold focus:outline-none focus:border-[#6E63D9]"
                />
                @error('new_password') <span class="text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2 border-t border-[#ECE8F7] dark:border-[#2A2645] flex items-center justify-between">
                <button 
                    type="button" 
                    wire:click="$dispatch('close-profile-modal')" 
                    class="px-4 py-2.5 rounded-full bg-[#F8F5FF] dark:bg-[#25203D] text-[#7A7A92] dark:text-slate-300 font-bold border border-[#ECE8F7] dark:border-[#352F52]"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-full bg-[#6E63D9] hover:bg-[#5C52C7] text-white font-bold shadow-button transition-all"
                >
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</div>
