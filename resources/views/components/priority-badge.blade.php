@props(['priority' => 'medium'])

@php
    $configs = [
        'urgent' => [
            'class' => 'bg-[#FF6B81]/15 text-[#FF6B81] border-[#FF6B81]/30 dark:bg-[#FF6B81]/20 dark:text-[#FF8FA0]',
            'label' => 'Urgent',
            'dot' => 'bg-[#FF6B81]',
        ],
        'high' => [
            'class' => 'bg-[#6E63D9]/15 text-[#6E63D9] border-[#6E63D9]/30 dark:bg-[#6E63D9]/25 dark:text-[#A98BEF]',
            'label' => 'High',
            'dot' => 'bg-[#6E63D9]',
        ],
        'medium' => [
            'class' => 'bg-blue-500/15 text-blue-600 border-blue-500/30 dark:bg-blue-500/20 dark:text-blue-400',
            'label' => 'Medium',
            'dot' => 'bg-blue-500',
        ],
        'low' => [
            'class' => 'bg-[#72D49A]/20 text-[#2AA857] dark:text-[#72D49A] border-[#72D49A]/40',
            'label' => 'Low',
            'dot' => 'bg-[#72D49A]',
        ],
    ];

    $config = $configs[strtolower($priority)] ?? $configs['medium'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $config['class'] }} tracking-wide transition-all">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
