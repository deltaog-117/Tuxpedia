@extends('layouts.app')

@section('title', 'What links to ' . $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">What links here</span>
    </nav>

    <header class="page-header">
        <h1 class="page-title">What links to {{ $page->title }}</h1>
        <a href="{{ route('wiki.show', $page->slug) }}" class="btn-secondary">Back to article</a>
    </header>

    @if($pages->isEmpty())
        <div class="card p-6 text-sm text-tux-muted">
            No other page links here yet. Write <code class="font-mono">[[{{ $page->title }}]]</code> in an article to link it.
        </div>
    @else
        <p class="mb-4 text-sm text-tux-muted">{{ $pages->count() }} {{ Str::plural('page', $pages->count()) }}</p>
        <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pages as $linking)
                <li><a href="{{ route('wiki.show', $linking->slug) }}">{{ $linking->title }}</a></li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
