@extends('layouts.app')

@section('title', 'Category: ' . $category->name)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.categories') }}">Categories</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">{{ $category->name }}</span>
    </nav>

    <header class="page-header">
        <h1 class="page-title">{{ $category->name }}</h1>
        <a href="{{ route('wiki.categories') }}" class="btn-secondary">All categories</a>
    </header>

    @if($category->description)
        <p class="mb-4 text-tux-muted">{{ $category->description }}</p>
    @endif

    @if($pages->isEmpty())
        <div class="card p-6 text-sm text-tux-muted">No article is in this category.</div>
    @else
        <p class="mb-4 text-sm text-tux-muted">{{ $pages->count() }} {{ Str::plural('article', $pages->count()) }}</p>
        <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pages as $page)
                <li><a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a></li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
