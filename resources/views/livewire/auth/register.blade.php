<div class="w-full h-full flex items-center justify-center p-6 bg-[#F8F5FF] dark:bg-[#12101F]">
    <div class="w-full max-w-md bg-white dark:bg-[#1B182E] rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-8 shadow-soft-card space-y-6">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#6E63D9] via-[#8675E6] to-[#E98AC9] p-0.5 shadow-button mx-auto">
                <div class="w-full h-full bg-[#6658C8] rounded-[14px] flex items-center justify-center text-white font-black text-xl">
                    K
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-[#2F2F45] dark:text-white tracking-tight">Create Account</h1>
            <p class="text-xs font-semibold text-[#7A7A92] dark:text-[#A8A3C7]">Join your team on KanbanFlow</p>
        </div>

        <!-- Register Form -->
        <form wire:submit="register" class="space-y-4">
            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Full Name</label>
                <input 
                    type="text" 
                    wire:model="name" 
                    placeholder="e.g. Jordan Lee"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('name') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Email Address</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="jordan@kanban.test"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('email') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Job Title / Role</label>
                <input 
                    type="text" 
                    wire:model="role" 
                    placeholder="e.g. Frontend Architect"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('role') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Password</label>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="Minimum 6 characters"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('password') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full py-3 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] hover:from-[#5C52C7] hover:to-[#7866DD] text-white font-bold text-xs shadow-button transition-all transform hover:-translate-y-0.5">
                Create Free Account
            </button>
        </form>

        <!-- Footer Link to Login -->
        <div class="text-center pt-2">
            <p class="text-xs text-[#7A7A92] font-medium">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-extrabold text-[#6E63D9] hover:underline">Log in here</a>
            </p>
        </div>

    </div>
</div>
