<div class="space-y-4">
    <!-- Add Comment Box -->
    <div class="flex items-start gap-3">
        <img src="{{ $activeUser ? $activeUser->avatar_url : 'https://ui-avatars.com/api/?name=User' }}" class="w-8 h-8 rounded-full object-cover shrink-0 mt-0.5" alt="">
        <div class="flex-1 space-y-2">
            <textarea 
                wire:model="commentText" 
                rows="2" 
                placeholder="Write a comment..." 
                class="w-full px-4 py-2.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] text-[#2F2F45] dark:text-white text-xs font-medium focus:outline-none focus:border-[#6E63D9] resize-none"
            ></textarea>
            <div class="flex justify-end">
                <button 
                    wire:click="addComment" 
                    class="px-4 py-2 rounded-full bg-[#6E63D9] text-white text-xs font-bold shadow-button transition-all"
                >
                    Post Comment
                </button>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-3 pt-2">
        @forelse($comments as $cmt)
            <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#F8F5FF] dark:bg-[#25203D] border border-[#ECE8F7] dark:border-[#352F52] group">
                <img src="{{ $cmt->user ? $cmt->user->avatar_url : 'https://ui-avatars.com/api/?name=User' }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#2F2F45] dark:text-white">{{ $cmt->user ? $cmt->user->name : 'User' }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-semibold text-[#7A7A92]">{{ $cmt->created_at->diffForHumans() }}</span>
                            <button wire:click="deleteComment({{ $cmt->id }})" class="text-[#7A7A92] hover:text-[#FF6B81] opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-[#7A7A92] dark:text-slate-300 mt-1 leading-relaxed font-medium">{{ $cmt->comment }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-[#7A7A92] py-6 font-medium">No comments on this task yet. Start the conversation!</p>
        @endforelse
    </div>
</div>
