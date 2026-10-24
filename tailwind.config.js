/** @type {import('tailwindcss').Config} */

// Design tokens live in resources/css/app.css as space-separated RGB channels.
// Wrapping them here keeps Tailwind's opacity modifiers working (bg-tux-surface/80).
const token = (name) => `rgb(var(--tux-${name}) / <alpha-value>)`;

export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Features/**/*.php",
    "./app/Features/**/*.blade.php",
    // Pagination markup ships with dark: variants; scan it so they get generated.
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./vendor/livewire/livewire/src/Features/SupportPagination/views/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        'tux-bg': token('bg'),
        'tux-surface': token('surface'),
        'tux-border': token('border'),
        'tux-text': token('text'),
        'tux-muted': token('muted'),
        'tux-accent': token('accent'),
        'tux-solid': token('solid'),
        'tux-solid-hover': token('solid-hover'),
        'tux-amber': token('amber'),
        'tux-green': token('green'),
        'tux-orange': token('orange'),
        'tux-red': token('red'),
        // Kept for backwards compatibility: the old blue is now the accent.
        'tux-blue': token('accent'),
        'tux-dark': '#1e1e1e',
        'tux-terminal-bg': '#1e1e1e',
        'tux-terminal-text': '#d4d4d4',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Space Grotesk"', 'Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      keyframes: {
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(8px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        'fade-up': 'fade-up 0.4s ease-out both',
      },
    },
  },
  plugins: [],
}
