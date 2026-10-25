@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none" style="{{ $page->distribution?->accentStyle() }}">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">{{ $page->title }}</span>
    </nav>

    @if(session()->has('message'))
        <div class="alert alert--success">{{ session('message') }}</div>
    @endif

    <header class="page-header">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="page-title">{{ $page->title }}</h1>
                @if($page->wasUpdatedSinceLastVisit(auth()->user()))
                    <span class="badge-new">Updated since your last visit</span>
                @endif
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if($page->distribution)
                    <span class="chip chip--distro">
                        <span class="distro-dot" aria-hidden="true"></span>{{ $page->distribution->name }}
                    </span>
                @endif
                <span class="chip font-mono">/{{ $page->slug }}</span>
                @if($page->author)
                    <span class="chip">By {{ $page->author }}</span>
                @endif
                <span class="chip">
                    Updated <time datetime="{{ $page->updated_at->toIso8601String() }}">{{ $page->updated_at->format('Y-m-d H:i') }}</time>
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('wiki.history', $page->slug) }}" class="btn-ghost">History</a>
            <a href="{{ route('wiki.edit', $page->slug) }}" class="btn-secondary">Edit</a>
        </div>
    </header>

    @if($page->distribution)
        <div class="mb-6 lg:hidden">
            <x-distro-infobox :distribution="$page->distribution" />
        </div>
    @endif

    <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <article class="card p-6 sm:p-10">
            <div id="article-body" class="article">
                {!! $page->content->toHtml() !!}
            </div>
        </article>
        <aside class="hidden lg:block">
            @if($page->distribution)
                <div class="mb-6">
                    <x-distro-infobox :distribution="$page->distribution" />
                </div>
            @endif
            <nav id="toc" class="toc" aria-label="On this page" hidden></nav>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Builds "On this page" from the rendered h2/h3 headings and highlights the current one.
    (function () {
        var body = document.getElementById('article-body');
        var toc = document.getElementById('toc');
        if (!body || !toc) { return; }

        var headings = Array.prototype.slice.call(body.querySelectorAll('h2, h3'));
        if (headings.length < 2) { return; }

        var title = document.createElement('p');
        title.className = 'toc__title';
        title.textContent = 'On this page';
        toc.appendChild(title);

        var used = {};
        var links = headings.map(function (heading) {
            var base = heading.textContent.trim().toLowerCase()
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'section';
            var id = base;
            var n = 2;
            while (used[id]) { id = base + '-' + n++; }
            used[id] = true;
            heading.id = id;

            var link = document.createElement('a');
            link.href = '#' + id;
            link.textContent = heading.textContent;
            link.className = 'toc__link' + (heading.tagName === 'H3' ? ' toc__link--sub' : '');
            toc.appendChild(link);
            return link;
        });
        toc.hidden = false;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) { return; }
                links.forEach(function (link) { link.classList.remove('toc__link--active'); });
                links[headings.indexOf(entry.target)].classList.add('toc__link--active');
            });
        }, { rootMargin: '0px 0px -70% 0px' });
        headings.forEach(function (heading) { observer.observe(heading); });
    })();
</script>
@endpush
