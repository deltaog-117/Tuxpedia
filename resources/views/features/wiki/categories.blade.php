@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">Categories</span>
    </nav>

    <header class="page-header">
        <h1 class="page-title">Categories</h1>
        <a href="{{ route('wiki.all') }}" class="btn-secondary">All pages</a>
    </header>

    @if($categories->isEmpty())
        <div class="card p-6 text-sm text-tux-muted">No categories yet. Add some in the editor when you write an article.</div>
    @else
        <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('wiki.category', $category->slug) }}">{{ $category->name }}</a>
                    <span class="text-sm text-tux-muted">({{ $category->pages_count }})</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
