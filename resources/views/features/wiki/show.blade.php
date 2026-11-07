@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none" style="{{ $page->distribution?->accentStyle() }}">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted print:hidden">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">{{ $page->title }}</span>
    </nav>

    @if(session()->has('message'))
        <div class="alert alert--success print:hidden">{{ session('message') }}</div>
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
                    <a href="{{ route('distro.show', $page->distribution->slug) }}" class="chip chip--distro">
                        <span class="distro-dot" aria-hidden="true"></span>{{ $page->distribution->name }}
                    </a>
                @endif
                @if($page->kind && $page->kind !== \App\Features\Wiki\ValueObjects\PageKind::Article)
                    <a href="{{ route('wiki.glossary') }}" class="chip">{{ $page->kind->label() }}</a>
                @endif
                <span class="chip font-mono">/{{ $page->slug }}</span>
                @if($page->isProtected())
                    <span class="chip">Protected</span>
                @endif
                @if($page->author)
                    <span class="chip">By <x-author :name="$page->author" :user="$page->user" /></span>
                @endif
                <span class="chip">
                    Updated <time datetime="{{ $page->updated_at->toIso8601String() }}">{{ $page->updated_at->format('Y-m-d H:i') }}</time>
                </span>
            </div>
            @if($page->categories->isNotEmpty())
                <div class="mt-2 flex flex-wrap items-center gap-2" aria-label="Categories">
                    @foreach($page->categories as $category)
                        <a href="{{ route('wiki.category', $category->slug) }}" class="chip">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="flex gap-2 print:hidden">
            <a href="{{ route('wiki.backlinks', $page->slug) }}" class="btn-ghost">What links here</a>
            <a href="{{ route('wiki.talk', $page->slug) }}" class="btn-ghost">Talk ({{ $page->talkTopics()->count() }})</a>
            <a href="{{ route('wiki.history', $page->slug) }}" class="btn-ghost">History</a>
            <a href="{{ route('wiki.export', $page->slug) }}" class="btn-ghost">Markdown</a>
            <button type="button" class="btn-ghost" data-print>Print</button>
            @can('protect', $page)
                <form method="POST" action="{{ route('wiki.protect', $page->slug) }}">
                    @csrf
                    <button type="submit" class="btn-ghost">{{ $page->isProtected() ? 'Unprotect' : 'Protect' }}</button>
                </form>
            @endcan
            @can('update', $page)
                <a href="{{ route('wiki.edit', $page->slug) }}" class="btn-secondary">Edit</a>
            @endcan
        </div>
    </header>

    @if($page->distribution)
        <div class="mb-6 lg:hidden print:hidden">
            <x-distro-infobox :distribution="$page->distribution" />
        </div>
    @endif

    <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <article class="card p-6 sm:p-10">
            @if($page->kind === \App\Features\Wiki\ValueObjects\PageKind::Disambiguation)
                <p class="mb-6 text-sm italic text-tux-muted">
                    This page lists the articles that share the name “{{ $page->title }}”. If a link led you here, it may need to point straight at the page you meant.
                </p>
            @endif
            <div id="article-body" class="article">
                {!! $html !!}
            </div>
            @if($permalink)
                <p class="mt-8 border-t border-tux-border pt-4 text-sm text-tux-muted">
                    Permanent link to this version:
                    <a href="{{ route('wiki.revision', [$page->slug, $permalink->sha]) }}" class="font-mono">{{ route('wiki.revision', [$page->slug, $permalink->sha]) }}</a>
                </p>
            @endif
        </article>
        <aside class="hidden lg:block print:hidden">
            @if($page->distribution)
                <div class="mb-6">
                    <x-distro-infobox :distribution="$page->distribution" />
                </div>
            @endif
            @if($toc->isWorthShowing())
                <nav id="toc" class="toc" aria-label="On this page">
                    <p class="toc__title">On this page</p>
                    @foreach($toc->entries() as $entry)
                        <a href="#{{ $entry->id }}" class="toc__link{{ $entry->isSub() ? ' toc__link--sub' : '' }}">{{ $entry->text }}</a>
                    @endforeach
                </nav>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/highlight.js') }}" defer></script>
<script>
    // The contents list is rendered by the server; this only marks the heading being read.
    (function () {
        var toc = document.getElementById('toc');
        if (!toc || !window.IntersectionObserver) { return; }

        var links = Array.prototype.slice.call(toc.querySelectorAll('a'));
        var headings = links.map(function (link) {
            return document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) { return; }
                links.forEach(function (link) { link.classList.remove('toc__link--active'); });
                var index = headings.indexOf(entry.target);
                if (index !== -1) { links[index].classList.add('toc__link--active'); }
            });
        }, { rootMargin: '0px 0px -70% 0px' });
        headings.forEach(function (heading) { if (heading) { observer.observe(heading); } });
    })();
</script>
@endpush
