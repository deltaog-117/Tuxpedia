@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    @if(session()->has('message'))
        <div class="alert alert--success">{{ session('message') }}</div>
    @endif

    <header class="page-header">
        <div class="flex items-center gap-4">
            <x-avatar :user="$user" size="lg" />
            <div>
                <h1 class="page-title">{{ $user->name }}</h1>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <span class="chip font-mono">{{ $user->username }}</span>
                    <span class="chip">{{ ucfirst($user->role->value) }}</span>
                    <span class="chip">
                        Joined <time datetime="{{ $user->created_at->toIso8601String() }}">{{ $user->created_at->format('Y-m-d') }}</time>
                    </span>
                    <span class="chip">{{ $edits }} {{ Str::plural('edit', $edits) }}</span>
                </div>
            </div>
        </div>
        @can('update', $user)
            <a href="{{ route('profile.edit', $user->username) }}" class="btn-secondary">Edit profile</a>
        @endcan
    </header>

    @if($user->bio)
        <p class="mb-8 whitespace-pre-line break-words text-sm">{{ $user->bio }}</p>
    @endif

    <section class="mb-8" aria-labelledby="edited-heading">
        <h2 id="edited-heading" class="mb-3 text-lg font-semibold">Articles edited</h2>
        @if($pages->isEmpty())
            <div class="card p-6 text-sm text-tux-muted">No edits yet.</div>
        @else
            <div class="data-table__wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Edits</th>
                            <th>Last edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $row)
                            <tr>
                                <td><a href="{{ route('wiki.show', $row->page->slug) }}">{{ $row->page->title }}</a></td>
                                <td>{{ $row->edit_count }}</td>
                                <td><time>{{ \Illuminate\Support\Carbon::parse($row->last_edit)->format('Y-m-d H:i') }}</time></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section aria-labelledby="talk-heading">
        <h2 id="talk-heading" class="mb-3 text-lg font-semibold">Talk posts</h2>
        @if($talk->isEmpty())
            <div class="card p-6 text-sm text-tux-muted">No talk posts yet.</div>
        @else
            <div class="grid gap-3">
                @foreach($talk as $post)
                    <article class="card p-4">
                        <p class="mb-1 text-xs text-tux-muted">
                            {{ $post->isReply ? 'Replied in' : 'Opened' }}
                            <a href="{{ route('wiki.talk.topic', [$post->page->slug, $post->topicId]) }}">{{ $post->topicTitle }}</a>
                            on <a href="{{ route('wiki.show', $post->page->slug) }}">{{ $post->page->title }}</a>
                            · <time datetime="{{ $post->at->toIso8601String() }}">{{ $post->at->format('Y-m-d H:i') }}</time>
                        </p>
                        <p class="break-words text-sm">{{ $post->excerpt }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
