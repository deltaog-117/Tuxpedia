<form wire:submit.prevent="search" class="search" role="search">
    <svg class="search__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
    </svg>
    <input wire:model="query" type="search" placeholder="Search wiki…" class="search__input" aria-label="Search" data-search-input>
    <kbd class="search__hint" aria-hidden="true">/</kbd>
    <button type="submit" class="sr-only">Search</button>
</form>
