<div>
    @if(!empty($query))
        @if($results && $results->count() > 0)
            <p class="mb-4 text-tux-muted">Found {{ $results->total() }} result(s) for "<span class="font-medium text-tux-text">{{ $query }}</span>"</p>
            <div class="space-y-4">
                @foreach($results as $page)
                    <a href="{{ route('wiki.show', $page->slug) }}" class="card card--interactive block p-5 hover:no-underline">
                        <h2 class="text-xl font-semibold text-tux-accent">{{ $page->title }}</h2>
                        <p class="mt-1 text-sm text-tux-muted">
                            {{ Str::limit(strip_tags($page->content), 200) }}
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-tux-muted">
                            <span>Updated {{ $page->updated_at->diffForHumans() }}</span>
                            @if(isset($page->relevance))
                                <span class="chip">Relevance: {{ number_format($page->relevance, 2) }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $results->appends(['q' => request('q')])->links() }}
            </div>
        @else
            <div class="card border-dashed p-10 text-center text-tux-muted">No results found for "{{ $query }}".</div>
        @endif
    @else
        <div class="card border-dashed p-10 text-center text-tux-muted">Enter a search term above.</div>
    @endif
</div>
