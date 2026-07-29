<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark',
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" x-init="
    if (darkMode) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
" @keydown.window="
    if ($event.key === 'n' && !$event.target.matches('input, textarea, select')) {
        $event.preventDefault();
        Livewire.dispatch('open-task-modal');
    }
    if ($event.key === '/' && !$event.target.matches('input, textarea, select')) {
        $event.preventDefault();
        document.getElementById('task-search-input')?.focus();
    }
    if ($event.key === '?') {
        Livewire.dispatch('toggle-shortcuts-modal');
    }
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kanban Board') }} - Modern Soft UI</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#F8F5FF] dark:bg-[#12101F] text-[#2F2F45] dark:text-[#F2EEFF] h-screen w-screen overflow-hidden flex flex-col transition-colors duration-300">
    
    <!-- Full-bleed Workspace Container -->
    <div class="w-full h-screen flex overflow-hidden">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
