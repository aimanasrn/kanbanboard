<div class="w-full h-full flex items-center justify-center p-6 bg-[#F8F5FF] dark:bg-[#12101F]">
    <div class="w-full max-w-md bg-white dark:bg-[#1B182E] rounded-[24px] border border-[#ECE8F7] dark:border-[#2A2645] p-8 shadow-soft-card space-y-6">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#6E63D9] via-[#8675E6] to-[#E98AC9] p-0.5 shadow-button mx-auto">
                <div class="w-full h-full bg-[#6658C8] rounded-[14px] flex items-center justify-center text-white font-black text-xl">
                    K
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-[#2F2F45] dark:text-white tracking-tight">Welcome Back</h1>
            <p class="text-xs font-semibold text-[#7A7A92] dark:text-[#A8A3C7]">Log in to access your Soft Minimalist Kanban workspace</p>
        </div>

        <!-- Login Form -->
        <form wire:submit="login" class="space-y-4">
            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Email Address</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="alex@kanban.test"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('email') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-[#2F2F45] dark:text-slate-300">Password</label>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-xs font-semibold text-[#2F2F45] dark:text-white focus:outline-none focus:border-[#6E63D9]"
                />
                @error('password') <span class="text-xs text-[#FF6B81] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded-md text-[#6E63D9] focus:ring-[#6E63D9]" />
                    <span class="font-bold text-[#7A7A92] dark:text-slate-300">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-full bg-gradient-to-r from-[#6E63D9] to-[#8675E6] hover:from-[#5C52C7] hover:to-[#7866DD] text-white font-bold text-xs shadow-button transition-all transform hover:-translate-y-0.5">
                Sign In
            </button>
        </form>

        <!-- Quick Demo Login Shortcuts -->
        <div class="pt-4 border-t border-[#ECE8F7] dark:border-[#2A2645] space-y-2">
            <span class="block text-[11px] font-extrabold text-[#7A7A92] uppercase text-center">⚡ 1-Click Demo Login</span>
            <div class="grid grid-cols-2 gap-2">
                @foreach($demoUsers as $u)
                    <button 
                        type="button" 
                        wire:click="loginAs('{{ $u->email }}')" 
                        class="p-2 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] hover:bg-[#6E63D9]/10 border border-[#ECE8F7] dark:border-[#352F52] text-left flex items-center gap-2 transition-all"
                    >
                        <img src="{{ $u->avatar_url }}" class="w-6 h-6 rounded-lg object-cover" alt="">
                        <div class="min-w-0">
                            <span class="block text-[11px] font-bold text-[#2F2F45] dark:text-white truncate">{{ $u->name }}</span>
                            <span class="block text-[9px] text-[#7A7A92] truncate">{{ $u->role }}</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Footer Link to Register -->
        <div class="text-center pt-2">
            <p class="text-xs text-[#7A7A92] font-medium">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-extrabold text-[#6E63D9] hover:underline">Sign up for free</a>
            </p>
        </div>

    </div>
</div>
