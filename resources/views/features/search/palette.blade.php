<div x-data="{
        open: false,
        active: 0,
        show() { this.open = true; this.active = 0; this.$nextTick(() => this.$refs.input.focus()); },
        hide() { this.open = false; $wire.query = ''; },
        count() { return this.$refs.list.querySelectorAll('[data-palette-index]').length; },
        move(step) {
            const total = this.count();
            if (total === 0) { return; }
            this.active = (this.active + step + total) % total;
            this.$nextTick(() => this.$refs.list.querySelector('[data-palette-index=\'' + this.active + '\']')?.scrollIntoView({ block: 'nearest' }));
        },
        choose() { this.$refs.list.querySelector('[data-palette-index=\'' + this.active + '\']')?.click(); },
    }"
    @palette-open.window="show()"
    @keydown.escape.window="open && hide()">
    <div x-show="open" x-cloak class="palette" role="dialog" aria-modal="true" aria-label="Command palette" @click.self="hide()">
        <div class="palette__panel">
            <input x-ref="input" wire:model.live.debounce.150ms="query" type="text" class="palette__input"
                   placeholder="Search pages or jump to…" aria-label="Search pages or jump to" autocomplete="off" spellcheck="false"
                   @input="active = 0"
                   @keydown.down.prevent="move(1)"
                   @keydown.up.prevent="move(-1)"
                   @keydown.enter.prevent="choose()">

            <div x-ref="list" class="palette__list" role="listbox">
                @php
                    $rows = array_merge($commands, $pages);
                    $previous = null;
                @endphp
                @forelse($rows as $index => $item)
                    @if($item->group !== $previous)
                        <p class="palette__group">{{ $item->group }}</p>
                        @php $previous = $item->group; @endphp
                    @endif

                    @if($item->url !== null)
                        <a href="{{ $item->url }}" class="palette__item" role="option" data-palette-index="{{ $index }}"
                           x-bind:aria-selected="(active === {{ $index }}).toString()" @mouseenter="active = {{ $index }}">{{ $item->label }}</a>
                    @else
                        <button type="button" class="palette__item" role="option" data-palette-index="{{ $index }}"
                                x-bind:aria-selected="(active === {{ $index }}).toString()" @mouseenter="active = {{ $index }}"
                                @click="document.querySelector('[data-theme-toggle]').click(); hide()">{{ $item->label }}</button>
                    @endif
                @empty
                    <p class="palette__empty">Nothing matches "{{ $query }}".</p>
                @endforelse
            </div>

            <div class="palette__footer" aria-hidden="true">
                <span><kbd>↑</kbd> <kbd>↓</kbd> move</span>
                <span><kbd>↵</kbd> open</span>
                <span><kbd>esc</kbd> close</span>
            </div>
        </div>
    </div>
</div>
