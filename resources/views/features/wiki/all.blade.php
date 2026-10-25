@extends('layouts.app')

@section('title', 'All pages')

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">All pages</span>
    </nav>

    <header class="page-header">
        <h1 class="page-title">All pages</h1>
        <a href="{{ route('wiki.recent') }}" class="btn-secondary">Recent changes</a>
    </header>

    @if($total === 0)
        <div class="card p-6 text-sm text-tux-muted">There are no pages yet.</div>
    @else
        <p class="mb-4 text-sm text-tux-muted">{{ $total }} {{ Str::plural('article', $total) }}</p>

        <nav aria-label="Jump to letter" class="mb-8 flex flex-wrap gap-2">
            @foreach(array_keys($groups) as $letter)
                <a href="#letter-{{ $letter === '#' ? 'other' : $letter }}" class="btn-ghost font-mono">{{ $letter }}</a>
            @endforeach
        </nav>

        @foreach($groups as $letter => $pages)
            <section class="mb-8" aria-labelledby="letter-{{ $letter === '#' ? 'other' : $letter }}">
                <h2 id="letter-{{ $letter === '#' ? 'other' : $letter }}" class="section-label">{{ $letter }}</h2>
                <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($pages as $page)
                        <li><a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    @endif
</div>
@endsection
