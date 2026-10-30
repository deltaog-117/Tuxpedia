@extends('layouts.app')

@section('title', 'History of ' . $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">History</span>
    </nav>

    @if(session()->has('message'))
        <div class="alert alert--success">{{ session('message') }}</div>
    @endif

    <header class="page-header">
        <h1 class="page-title">Revision history</h1>
        <a href="{{ route('wiki.show', $page->slug) }}" class="btn-secondary">Back to article</a>
    </header>

    @if($revisions->isEmpty())
        <div class="card p-6 text-sm text-tux-muted">
            No revisions are recorded for this page yet. Saving it creates the first one.
        </div>
    @else
        <div class="data-table__wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Revision</th>
                        <th>Date</th>
                        <th>Author</th>
                        <th>Summary</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revisions as $revision)
                        @php($previous = $revisions->get($loop->index + 1))
                        <tr>
                            <td class="font-mono">
                                <a href="{{ route('wiki.revision', [$page->slug, $revision->sha]) }}">{{ $revision->shortSha() }}</a>
                                @if($loop->first)<span class="badge-new ml-2">Current</span>@endif
                            </td>
                            <td><time datetime="{{ $revision->created_at->toIso8601String() }}">{{ $revision->created_at->format('Y-m-d H:i') }}</time></td>
                            <td><x-author :name="$revision->author" :user="$revision->user" /></td>
                            <td>{{ $revision->summary ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if($previous)
                                        <a href="{{ route('wiki.diff', [$page->slug, 'from' => $previous->sha, 'to' => $revision->sha]) }}" class="btn-ghost">Changes</a>
                                    @endif
                                    @if(! $loop->first)
                                        @can('revert', $page)
                                        <form method="POST" action="{{ route('wiki.revert', [$page->slug, $revision->sha]) }}">
                                            @csrf
                                            <button type="submit" class="btn-secondary">Restore</button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
