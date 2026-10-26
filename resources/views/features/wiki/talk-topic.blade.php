@extends('layouts.app')

@section('title', $topic->title . ' - Talk: ' . $page->title)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('wiki.index') }}">Wiki</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('wiki.talk', $page->slug) }}">Talk</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">{{ $topic->title }}</span>
    </nav>

    @if(session()->has('message'))
        <div class="alert alert--success">{{ session('message') }}</div>
    @endif

    <header class="page-header">
        <h1 class="page-title">{{ $topic->title }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('wiki.talk', $page->slug) }}" class="btn-secondary">All topics</a>
            @can('moderate', $page)
                <form method="POST" action="{{ route('wiki.talk.destroy', [$page->slug, $topic->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-ghost">Delete topic</button>
                </form>
            @endcan
        </div>
    </header>

    <div class="grid gap-4">
        <article class="card p-6">
            <p class="mb-2 text-xs text-tux-muted">
                <span class="font-medium text-tux-text">{{ $topic->author }}</span>
                <time datetime="{{ $topic->created_at->toIso8601String() }}">{{ $topic->created_at->format('Y-m-d H:i') }}</time>
            </p>
            <div class="whitespace-pre-line break-words text-sm">{{ $topic->body }}</div>
        </article>

        @foreach($topic->replies as $reply)
            <article class="card ml-4 p-6 sm:ml-8">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <p class="text-xs text-tux-muted">
                        <span class="font-medium text-tux-text">{{ $reply->author }}</span>
                        <time datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->format('Y-m-d H:i') }}</time>
                    </p>
                    @can('moderate', $page)
                        <form method="POST" action="{{ route('wiki.talk.reply.destroy', [$page->slug, $topic->id, $reply->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost">Delete</button>
                        </form>
                    @endcan
                </div>
                <div class="whitespace-pre-line break-words text-sm">{{ $reply->body }}</div>
            </article>
        @endforeach
    </div>

    <h2 class="mb-3 mt-8 text-lg font-semibold">Reply</h2>
    @auth
        <form method="POST" action="{{ route('wiki.talk.reply', [$page->slug, $topic->id]) }}" class="card grid gap-4 p-6">
            @csrf
            <div>
                <label for="body" class="sr-only">Message</label>
                <textarea id="body" name="body" rows="4" maxlength="{{ \App\Features\Wiki\ValueObjects\TalkBody::MAX_LENGTH }}" required
                    class="input-field @error('body') input-field--invalid @enderror">{{ old('body') }}</textarea>
                @error('body')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div><button type="submit" class="btn-primary">Post reply</button></div>
        </form>
    @else
        <div class="card p-6 text-sm text-tux-muted">
            <a href="{{ route('auth.login') }}">Log in</a> to reply.
        </div>
    @endauth
</div>
@endsection
