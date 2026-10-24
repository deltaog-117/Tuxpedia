<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Tuxpedia')</title>
    <script>
        // Apply the saved (or system) theme before first paint to avoid a light flash.
        (function () {
            try {
                var saved = localStorage.getItem('tux-theme');
                var dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) { document.documentElement.classList.add('dark'); }
            } catch (e) {}
        })();
    </script>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/tuxpedia-logo.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col">
    @php
        $navLinks = [
            ['label' => 'Wiki', 'route' => 'wiki.index', 'match' => ['wiki.index', 'wiki.show', 'wiki.edit']],
            ['label' => 'New Page', 'route' => 'wiki.create', 'match' => ['wiki.create']],
            ['label' => 'Distros', 'route' => 'distro-comparison.index', 'match' => ['distro-comparison.*']],
            ['label' => 'Family Tree', 'route' => 'family-tree.index', 'match' => ['family-tree.*']],
            ['label' => 'Terminal', 'route' => 'terminal.index', 'match' => ['terminal.*']],
        ];
    @endphp

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-md focus:bg-tux-surface focus:px-3 focus:py-2">Skip to content</a>

    <nav class="site-nav" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <a href="{{ route('wiki.index') }}" class="site-nav__brand">
                        <img src="{{ asset('images/tuxpedia-logo.svg') }}" alt="" width="28" height="28" class="h-7 w-7"> Tuxpedia
                    </a>
                    <div class="hidden items-center gap-1 lg:flex">
                        @foreach($navLinks as $link)
                            @php $active = request()->routeIs(...$link['match']); @endphp
                            <a href="{{ route($link['route']) }}"
                               class="site-nav__link {{ $active ? 'site-nav__link--active' : '' }}"
                               @if($active) aria-current="page" @endif>{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="hidden lg:block lg:w-44 xl:w-60">
                        <livewire:search-bar />
                    </div>
                    <div class="hidden items-center gap-1 lg:flex">
                        @auth
                            <span class="hidden px-2 text-sm text-tux-muted xl:inline">{{ auth()->user()->name }}</span>
                            <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-ghost">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('auth.login') }}" class="site-nav__link">Login</a>
                            <a href="{{ route('auth.register') }}" class="btn-primary">Register</a>
                        @endauth
                    </div>

                    <button type="button" class="icon-button" data-theme-toggle aria-label="Toggle dark mode">
                        <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                        <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </button>

                    <button type="button" class="icon-button lg:hidden" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle menu">
                        <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="open" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-cloak x-transition class="space-y-1 border-t border-tux-border py-3 lg:hidden">
                <div class="mb-3">
                    <livewire:search-bar />
                </div>
                @foreach($navLinks as $link)
                    @php $active = request()->routeIs(...$link['match']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="site-nav__link block {{ $active ? 'site-nav__link--active' : '' }}"
                       @if($active) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endforeach
                @auth
                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="site-nav__link block w-full text-left">Logout ({{ auth()->user()->name }})</button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="site-nav__link block">Login</a>
                    <a href="{{ route('auth.register') }}" class="site-nav__link block">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main id="main" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="max-w-7xl mx-auto px-4 py-6 text-sm text-tux-muted text-center">
            Tuxpedia – the Linux encyclopedia. Built with ❤️ and Laravel.
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var dark = document.documentElement.classList.toggle('dark');
                    try { localStorage.setItem('tux-theme', dark ? 'dark' : 'light'); } catch (e) {}
                });
            });

            // "/" or Ctrl/Cmd+K focuses the first visible search box.
            document.addEventListener('keydown', function (event) {
                var active = document.activeElement;
                var typing = active && /^(INPUT|TEXTAREA|SELECT)$/.test(active.tagName);
                var inTerminal = active && active.closest('#terminal-container');
                var wantsSearch = (event.key === 'k' && (event.ctrlKey || event.metaKey) && !inTerminal)
                    || (event.key === '/' && !typing);
                if (!wantsSearch) { return; }

                var input = Array.prototype.find.call(
                    document.querySelectorAll('[data-search-input]'),
                    function (el) { return el.offsetParent !== null; }
                );
                if (input) {
                    event.preventDefault();
                    input.focus();
                    input.select();
                }
            });
        })();
    </script>
</body>
</html>
