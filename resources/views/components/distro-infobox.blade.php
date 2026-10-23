@props(['distribution'])

@php
    $rows = [
        'Based on' => $distribution->based_on,
        'Release model' => $distribution->release_model,
        'Package manager' => $distribution->package_manager,
        'Default desktop' => $distribution->default_desktop,
        'Architecture' => $distribution->architecture,
    ];
@endphp

<section class="infobox" style="{{ $distribution->accentStyle() }}" aria-label="{{ $distribution->name }} at a glance">
    <p class="infobox__title">
        @if(($logo = $distribution->logoUrl()) && Str::endsWith($logo, '.png'))
            <img src="{{ $logo }}" alt="" class="h-5 w-auto shrink-0 rounded bg-white px-1">
        @elseif($logo)
            <span class="logo-glyph logo-glyph--accent" style="--logo: url('{{ $logo }}')" aria-hidden="true"></span>
        @endif
        {{ $distribution->name }}
    </p>
    @foreach($rows as $label => $value)
        @if($value)
            <div class="infobox__row">
                <span class="infobox__label">{{ $label }}</span>
                <span class="text-tux-text">{{ $value }}</span>
            </div>
        @endif
    @endforeach
    @if($distribution->website && preg_match('#^https?://#i', $distribution->website))
        <div class="infobox__row">
            <span class="infobox__label">Website</span>
            <a href="{{ $distribution->website }}" rel="noopener noreferrer" class="truncate">{{ $distribution->website }}</a>
        </div>
    @endif
</section>
