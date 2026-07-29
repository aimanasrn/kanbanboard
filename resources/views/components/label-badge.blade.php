@props(['label' => '', 'color' => 'purple'])

@php
    $colorClasses = [
        'purple' => 'bg-[#6E63D9]/10 text-[#6E63D9] border-[#6E63D9]/20 dark:bg-[#6E63D9]/20 dark:text-[#A98BEF]',
        'lavender' => 'bg-[#A98BEF]/15 text-[#6E63D9] border-[#A98BEF]/30 dark:bg-[#A98BEF]/20 dark:text-[#A98BEF]',
        'pink' => 'bg-[#E98AC9]/15 text-[#D056A8] border-[#E98AC9]/30 dark:text-[#E98AC9]',
        'indigo' => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20 dark:text-indigo-400',
        'emerald' => 'bg-[#72D49A]/20 text-[#258B47] border-[#72D49A]/30 dark:text-[#72D49A]',
        'amber' => 'bg-[#FFC857]/20 text-[#B88414] border-[#FFC857]/30 dark:text-[#FFC857]',
        'blue' => 'bg-blue-500/10 text-blue-600 border-blue-500/20 dark:text-blue-400',
    ];

    $class = $colorClasses[$color] ?? $colorClasses['purple'];
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold border {{ $class }} tracking-wide">
    #{{ $label }}
</span>
