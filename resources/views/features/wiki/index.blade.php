@extends('layouts.app')

@section('title', 'All Wiki Pages')

@section('content')
<section class="hero animate-fade-up motion-reduce:animate-none">
    <span class="hero__eyebrow"><span aria-hidden="true">🐧</span> The community Linux encyclopedia</span>
    <h1 class="hero__title">
        Everything Linux,<br>
        <span class="hero__title-accent">in one place.</span>
    </h1>
    <p class="hero__lead">
        Read distro guides, compare distributions side by side, explore the family tree,
        and try commands in a browser terminal.
    </p>
    <div class="hero__search">
        <livewire:search-bar />
    </div>
    <div class="hero__actions">
        <a href="{{ route('distro-comparison.index') }}" class="btn-primary">Compare distros</a>
        <a href="{{ route('family-tree.index') }}" class="btn-secondary">Family tree</a>
        <a href="{{ route('terminal.index') }}" class="btn-ghost">Try the terminal</a>
    </div>
</section>

<section class="mt-12" aria-labelledby="pages-heading">
    <div class="page-header">
        <div>
            <h2 id="pages-heading" class="page-title text-2xl sm:text-3xl">Wiki pages</h2>
            <p class="page-lead">{{ $pages->count() }} {{ Str::plural('page', $pages->count()) }}</p>
        </div>
        <a href="{{ route('wiki.create') }}" class="btn-primary">+ New Page</a>
    </div>

    @if($pages->isEmpty())
        <div class="card border-dashed p-12 text-center">
            <p class="text-tux-muted">No pages yet. Be the first to create one!</p>
            <a href="{{ route('wiki.create') }}" class="btn-primary mt-4">Create the first page</a>
        </div>
    @else
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pages as $page)
                <li class="animate-fade-up motion-reduce:animate-none" style="animation-delay: {{ min($loop->index, 8) * 40 }}ms">
                    <a href="{{ route('wiki.show', $page->slug) }}" class="card card--interactive flex h-full gap-4 p-5 hover:no-underline" style="{{ $page->distribution?->accentStyle() }}">
                        <span class="card__mark {{ $page->distribution?->accent ? 'card__mark--distro' : '' }}" aria-hidden="true">{{ Str::upper(Str::substr($page->title, 0, 1)) }}</span>
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
