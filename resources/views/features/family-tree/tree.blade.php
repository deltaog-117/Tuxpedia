<div class="space-y-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($families as $family)
            <section class="tree-card" @if($family['accent']) style="--tree-accent: {{ $family['accent'] }}" @endif>
                <ul class="tree-list tree-list--root">
                    @include('features.family-tree.partials.node', ['node' => $family])
                </ul>
            </section>
        @endforeach
    </div>

    @if($independent->isNotEmpty())
        <section>
            <h2 class="mb-1 text-lg font-semibold">Independent</h2>
            <p class="mb-3 text-sm text-tux-muted">Built from scratch, with no derivatives in the wiki.</p>
            <div class="flex flex-wrap gap-2">
                @foreach($independent as $distro)
                    @include('features.family-tree.partials.chip', ['node' => $distro])
                @endforeach
            </div>
        </section>
    @endif
</div>
