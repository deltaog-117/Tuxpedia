@extends('layouts.app')

@section('title', 'Changes to ' . $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.history', $page->slug) }}">History</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">Changes</span>
    </nav>

    <header class="page-header">
        <div>
            <h1 class="page-title">Changes</h1>
            <p class="page-lead">
                <a class="font-mono" href="{{ route('wiki.revision', [$page->slug, $from->sha]) }}">{{ $from->shortSha() }}</a>
                →
                <a class="font-mono" href="{{ route('wiki.revision', [$page->slug, $to->sha]) }}">{{ $to->shortSha() }}</a>
            </p>
        </div>
        <a href="{{ route('wiki.history', $page->slug) }}" class="btn-secondary">Back to history</a>
    </header>

    <div class="diff" role="table" aria-label="Line changes">
        @foreach($lines as $line)
            <div role="row" class="diff__line diff__line--{{ $line->kind->value }}">
                <span class="diff__sign" aria-hidden="true">{{ match ($line->kind->value) { 'added' => '+', 'removed' => '−', default => ' ' } }}</span>
                <span role="cell">
                    @if($line->kind->value !== 'same')<span class="sr-only">{{ $line->kind->value }}:</span>@endif{{ $line->text }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endsection
