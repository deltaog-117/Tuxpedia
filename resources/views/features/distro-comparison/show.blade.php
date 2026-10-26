@extends('layouts.app')

@php($distribution = $profile->distribution)

@section('title', $distribution->name)

@section('content')
<div class="animate-fade-up motion-reduce:animate-none" style="{{ $distribution->accentStyle() }}">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-tux-muted">
        <a href="{{ route('distro-comparison.index') }}">Distributions</a>
        <span aria-hidden="true">/</span>
        <span class="text-tux-text">{{ $distribution->name }}</span>
    </nav>

    <header class="page-header">
        <div>
            <h1 class="page-title">{{ $distribution->name }}</h1>
            @if($distribution->description)
                <p class="mt-3 max-w-prose text-tux-muted">{{ $distribution->description }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            @if($article)
                <a href="{{ route('wiki.show', $article->slug) }}" class="btn-secondary">Read the article</a>
            @endif
            <a href="{{ route('family-tree.index') }}" class="btn-ghost">Family tree</a>
        </div>
    </header>

    <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <div class="space-y-8">
            <section aria-labelledby="family-heading" class="card p-6 sm:p-8">
                <h2 id="family-heading" class="mb-4 text-lg font-semibold">Family</h2>

                @if(count($profile->lineage) === 0)
                    <p class="text-sm text-tux-muted">{{ $distribution->name }} is independent: it is not based on another distribution.</p>
                @else
                    <p class="mb-2 text-sm text-tux-muted">Ancestry</p>
                    <ol class="flex flex-wrap items-center gap-2" aria-label="Ancestry">
                        @foreach($profile->lineage as $ancestor)
                            <li class="flex items-center gap-2">
                                @if($ancestor['distribution'])
                                    <a href="{{ route('distro.show', $ancestor['distribution']->slug) }}" class="chip">{{ $ancestor['name'] }}</a>
                                @else
                                    <span class="chip">{{ $ancestor['name'] }}</span>
                                @endif
                                <span aria-hidden="true" class="text-tux-muted">&rarr;</span>
                            </li>
                        @endforeach
                        <li><span class="chip chip--distro"><span class="distro-dot" aria-hidden="true"></span>{{ $distribution->name }}</span></li>
                    </ol>
                @endif

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="mb-2 text-sm font-semibold">Derivatives</h3>
                        @if($profile->derivatives->isEmpty())
                            <p class="text-sm text-tux-muted">No distribution is based directly on {{ $distribution->name }}.</p>
                        @else
                            <ul class="space-y-1">
                                @foreach($profile->derivatives as $derivative)
                                    <li><a href="{{ route('distro.show', $derivative->slug) }}">{{ $derivative->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div>
                        <h3 class="mb-2 text-sm font-semibold">Siblings</h3>
                        @if($profile->siblings->isEmpty())
                            <p class="text-sm text-tux-muted">No other distribution shares this parent.</p>
                        @else
                            <ul class="space-y-1">
                                @foreach($profile->siblings as $sibling)
                                    <li><a href="{{ route('distro.show', $sibling->slug) }}">{{ $sibling->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>

            <section aria-labelledby="related-heading" class="card p-6 sm:p-8">
                <h2 id="related-heading" class="mb-4 text-lg font-semibold">Related articles</h2>

                @if($article === null)
                    <p class="text-sm text-tux-muted">
                        There is no article about {{ $distribution->name }} yet.
                        <a href="{{ route('wiki.create', ['slug' => $distribution->slug]) }}">Write one</a>.
                    </p>
                @elseif($related->isEmpty())
                    <p class="text-sm text-tux-muted">No other article links to {{ $article->title }} or shares a category with it.</p>
                @else
                    <ul class="grid gap-x-8 gap-y-1 sm:grid-cols-2">
                        @foreach($related as $page)
                            <li><a href="{{ route('wiki.show', $page->slug) }}">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <aside>
            <x-distro-infobox :distribution="$distribution" :link="false" />
        </aside>
    </div>
</div>
@endsection
