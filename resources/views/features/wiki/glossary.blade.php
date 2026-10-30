@extends('layouts.app')

@section('title', 'Glossary')

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">Glossary</span>
    </nav>

    <header class="page-header">
        <div>
            <h1 class="page-title">Glossary</h1>
            <p class="page-lead">Core Linux concepts, and the names that can mean more than one thing.</p>
        </div>
        <a href="{{ route('wiki.all') }}" class="btn-secondary">All pages</a>
    </header>

    <section aria-labelledby="concepts-heading">
        <h2 id="concepts-heading" class="section-label">Concepts</h2>
        @if($concepts->isEmpty())
            <div class="card p-6 text-sm text-tux-muted">No concept pages yet. Set a page's type to Concept in the editor.</div>
        @else
            <ul class="grid gap-4 sm:grid-cols-2">
                @foreach($concepts as $concept)
                    <li class="card p-5">
                        <a href="{{ route('wiki.show', $concept->slug) }}" class="font-semibold">{{ $concept->title }}</a>
                        <p class="mt-1 text-sm text-tux-muted">{{ $concept->content->toExcerpt() }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="mt-10" aria-labelledby="ambiguous-heading">
        <h2 id="ambiguous-heading" class="section-label">Ambiguous names</h2>
        @if($ambiguous->isEmpty())
            <div class="card p-6 text-sm text-tux-muted">No disambiguation pages yet.</div>
        @else
            <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($ambiguous as $page)
                    <li><a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a></li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
