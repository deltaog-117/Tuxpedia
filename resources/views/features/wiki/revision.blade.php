@extends('layouts.app')

@section('title', $page->title . ' (revision ' . $revision->shortSha() . ')')

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.history', $page->slug) }}">History</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text font-mono">{{ $revision->shortSha() }}</span>
    </nav>

    <div class="alert {{ $isCurrent ? 'alert--success' : 'alert--error' }}">
        @if($isCurrent)
            This is the current revision.
        @else
            You are viewing an old revision, saved
            <time datetime="{{ $revision->created_at->toIso8601String() }}">{{ $revision->created_at->format('Y-m-d H:i') }}</time>.
            It may differ from the current article.
        @endif
    </div>

    <header class="page-header">
        <h1 class="page-title">{{ $page->title }}</h1>
        @if(! $isCurrent)
            @can('revert', $page)
            <form method="POST" action="{{ route('wiki.revert', [$page->slug, $revision->sha]) }}">
                @csrf
                <button type="submit" class="btn-secondary">Restore this revision</button>
            </form>
            @endcan
        @endif
    </header>

    <article class="card p-6 sm:p-10">
        <div class="article">
            {!! $html !!}
        </div>
    </article>
</div>
@endsection
