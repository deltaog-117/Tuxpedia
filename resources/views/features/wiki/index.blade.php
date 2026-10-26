@extends('layouts.app')

@section('title', 'All Wiki Pages')

@section('content')
<header class="masthead animate-fade-up motion-reduce:animate-none">
    <img src="{{ asset('images/tuxpedia-logo.svg') }}" alt="" width="64" height="64" class="mx-auto mb-4 h-14 w-14 sm:h-16 sm:w-16">
    <h1 class="masthead__title">Tuxpedia</h1>
    <p class="masthead__tagline">The free Linux encyclopedia</p>
    <div class="masthead__search">
        <livewire:search-bar />
    </div>
    <p class="masthead__stats">
        {{ $distributionCount }} {{ Str::plural('distribution', $distributionCount) }}
        <span aria-hidden="true">·</span>
        {{ $pages->count() }} {{ Str::plural('article', $pages->count()) }}
    </p>
    <nav aria-label="Browse" class="mt-4 flex flex-wrap justify-center gap-2">
        <a href="{{ route('wiki.random') }}" class="btn-ghost">Random article</a>
        <a href="{{ route('wiki.recent') }}" class="btn-ghost">Recent changes</a>
        <a href="{{ route('wiki.all') }}" class="btn-ghost">All pages</a>
    </nav>
</header>

<div class="mt-10 grid gap-8 lg:grid-cols-3">
    @if($featured)
        <section class="lg:col-span-2" aria-labelledby="featured-heading" style="{{ $featured->distribution?->accentStyle() }}">
            <h2 id="featured-heading" class="section-label">Featured article</h2>
            <article class="featured">
                <div class="flex items-center gap-4">
                    @include('features.wiki.partials.mark', ['page' => $featured])
                    <h3 class="featured__title">
                        <a href="{{ route('wiki.show', $featured->slug) }}">{{ $featured->title }}</a>
                    </h3>
                </div>
                <p class="featured__excerpt">{{ $featured->content->toExcerpt() }}</p>
                <a href="{{ route('wiki.show', $featured->slug) }}" class="featured__more">Read the full article →</a>
            </article>
        </section>
    @endif

    <aside aria-labelledby="portals-heading" class="{{ $featured ? '' : 'lg:col-span-3' }}">
        <h2 id="portals-heading" class="section-label">Portals</h2>
        <ul class="portals">
            <li>
                <a href="{{ route('distro-comparison.index') }}" class="portal">
                    <span class="portal__name">Compare distributions</span>
                    <span class="portal__text">Package managers, desktops and release models side by side.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('family-tree.index') }}" class="portal">
                    <span class="portal__name">Family tree</span>
                    <span class="portal__text">How distributions descend from one another.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('terminal.index') }}" class="portal">
                    <span class="portal__name">Terminal</span>
                    <span class="portal__text">Try commands in the browser.</span>
                </a>
            </li>
        </ul>
    </aside>
</div>

@if($pages->isNotEmpty())
    <section class="mt-12" aria-labelledby="families-heading">
        <h2 id="families-heading" class="section-label">Browse by family</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($families as $family => $members)
                <div>
                    <h3 class="family__name">{{ $family }}</h3>
                    <ul class="family__list">
                        @foreach($members as $member)
                            <li><a href="{{ route('wiki.show', $member->slug) }}">{{ $member->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>
@endif

<section class="mt-12" aria-labelledby="pages-heading">
    <div class="page-header">
        <div>
            <h2 id="pages-heading" class="page-title text-2xl sm:text-3xl">All articles</h2>
            <p class="page-lead">{{ $pages->count() }} {{ Str::plural('page', $pages->count()) }}</p>
        </div>
        @can('create', \App\Features\Wiki\Models\Page::class)
            <a href="{{ route('wiki.create') }}" class="btn-primary">+ New Page</a>
        @endcan
    </div>

    @if($pages->isEmpty())
        <div class="card border-dashed p-12 text-center">
            <p class="text-tux-muted">No pages yet. Be the first to create one!</p>
            @can('create', \App\Features\Wiki\Models\Page::class)
                <a href="{{ route('wiki.create') }}" class="btn-primary mt-4">Create the first page</a>
            @endcan
        </div>
    @else
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pages as $page)
                <li class="animate-fade-up motion-reduce:animate-none" style="animation-delay: {{ min($loop->index, 8) * 40 }}ms">
                    <a href="{{ route('wiki.show', $page->slug) }}" class="card card--interactive flex h-full gap-4 p-5 hover:no-underline" style="{{ $page->distribution?->accentStyle() }}">
                        @include('features.wiki.partials.mark', ['page' => $page])
                        <span class="min-w-0 flex-1">
                            <span class="flex items-start justify-between gap-2">
                                <span class="truncate font-semibold text-tux-text">{{ $page->title }}</span>
                                @if($page->wasUpdatedSinceLastVisit(auth()->user()))
                                    <span class="badge-new">New</span>
                                @endif
                            </span>
                            <span class="mt-1 block truncate font-mono text-xs text-tux-muted">/{{ $page->slug }}</span>
                            <span class="mt-3 block text-xs text-tux-muted">Updated {{ $page->updated_at->diffForHumans() }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
