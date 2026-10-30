@extends('layouts.app')

@section('title', 'Talk: ' . $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">Talk</span>
    </nav>

    @if(session()->has('message'))
        <div class="alert alert--success">{{ session('message') }}</div>
    @endif

    <header class="page-header">
        <h1 class="page-title">Talk: {{ $page->title }}</h1>
        <a href="{{ route('wiki.show', $page->slug) }}" class="btn-secondary">Back to article</a>
    </header>

    <p class="mb-6 text-sm text-tux-muted">
        Suggestions, questions and corrections about this article. Messages are plain text.
    </p>

    @if($topics->isEmpty())
        <div class="card mb-8 p-6 text-sm text-tux-muted">
            No topics yet. Start the first one below.
        </div>
    @else
        <div class="data-table__wrap mb-8">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Started by</th>
                        <th>Replies</th>
                        <th>Last activity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                        <tr>
                            <td><a href="{{ route('wiki.talk.topic', [$page->slug, $topic->id]) }}">{{ $topic->title }}</a></td>
                            <td><x-author :name="$topic->author" :user="$topic->user" /></td>
                            <td>{{ $topic->replies_count }}</td>
                            <td><time datetime="{{ $topic->updated_at->toIso8601String() }}">{{ $topic->updated_at->format('Y-m-d H:i') }}</time></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2 class="mb-3 text-lg font-semibold">New topic</h2>
    @auth
        <form method="POST" action="{{ route('wiki.talk.store', $page->slug) }}" class="card grid gap-4 p-6">
            @csrf
            <div>
                <label for="title" class="field-label">Title</label>
                <input id="title" name="title" type="text" maxlength="{{ \App\Features\Wiki\ValueObjects\TalkTitle::MAX_LENGTH }}" required
                    value="{{ old('title') }}" class="input-field @error('title') input-field--invalid @enderror">
                @error('title')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="body" class="field-label">Message</label>
                <textarea id="body" name="body" rows="5" maxlength="{{ \App\Features\Wiki\ValueObjects\TalkBody::MAX_LENGTH }}" required
                    class="input-field @error('body') input-field--invalid @enderror">{{ old('body') }}</textarea>
                @error('body')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div><button type="submit" class="btn-primary">Post topic</button></div>
        </form>
    @else
        <div class="card p-6 text-sm text-tux-muted">
            <a href="{{ route('auth.login') }}">Log in</a> to start a topic.
        </div>
    @endauth
</div>
@endsection
