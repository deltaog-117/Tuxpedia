<div>
    <div class="terminal-window">
        <div class="terminal-window__bar">
            <button type="button" class="terminal-window__dot bg-red-500" data-terminal-action="close" data-glyph="×" aria-label="Close terminal"></button>
            <button type="button" class="terminal-window__dot bg-yellow-400" data-terminal-action="minimize" data-glyph="−" aria-label="Minimize terminal"></button>
            <button type="button" class="terminal-window__dot bg-green-500" data-terminal-action="maximize" data-glyph="+" aria-label="Maximize terminal"></button>
            <span class="terminal-window__title">guest@tuxpedia: ~</span>
        </div>
        <div class="terminal-window__body">
            <div id="terminal-container" class="terminal-window__screen"></div>
        </div>
    </div>

    <!-- Load xterm.js from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm/css/xterm.css" />
    <script src="https://cdn.jsdelivr.net/npm/xterm/lib/xterm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit/lib/xterm-addon-fit.js"></script>

    <!-- Our terminal simulator script -->
    <script src="{{ asset('js/terminal-simulator.js') }}"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            if (typeof initTerminal === 'function') {
                initTerminal('terminal-container');
            } else {
                console.warn('Terminal simulator not loaded.');
            }
        });

        // Also run on DOM ready for direct page load
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof initTerminal === 'function') {
                // Wait a bit for the container to render
                setTimeout(function () {
                    initTerminal('terminal-container');
                }, 300);
            }
        });
    </script>

    <div class="mt-4 text-sm text-tux-muted">
        <p>Try commands: <code>ls -la</code>, <code>tree</code>, <code>cat /etc/os-release</code>, <code>neofetch</code>, <code>cowsay hi</code>, <code>help</code></p>
    </div>
</div>
