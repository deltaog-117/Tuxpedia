@extends('layouts.app')

@section('title', 'Recent changes')

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">Recent changes</span>
    </nav>

    <header class="page-header">
        <h1 class="page-title">Recent changes</h1>
        <a href="{{ route('wiki.all') }}" class="btn-secondary">All pages</a>
    </header>

    @if($revisions->isEmpty())
        <div class="card p-6 text-sm text-tux-muted">
            No changes are recorded yet. Saving a page creates the first one.
        </div>
    @else
        <div class="data-table__wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Page</th>
                        <th>Author</th>
                        <th>Summary</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revisions as $revision)
                        <tr>
                            <td><time datetime="{{ $revision->created_at->toIso8601String() }}">{{ $revision->created_at->format('Y-m-d H:i') }}</time></td>
                            <td><a href="{{ route('wiki.show', $revision->page->slug) }}">{{ $revision->page->title }}</a></td>
                            <td>{{ $revision->author ?? '—' }}</td>
                            <td>{{ $revision->summary ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if($revision->previous_sha)
                                        <a href="{{ route('wiki.diff', [$revision->page->slug, 'from' => $revision->previous_sha, 'to' => $revision->sha]) }}" class="btn-ghost">Changes</a>
                                    @else
                                        <span class="badge-new">New</span>
                                    @endif
                                    <a href="{{ route('wiki.history', $revision->page->slug) }}" class="btn-ghost">History</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $revisions->links() }}</div>
    @endif
</div>
@endsection
