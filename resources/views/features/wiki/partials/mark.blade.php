@php($logo = $page->distribution?->logoUrl())
<span class="card__mark {{ $page->distribution?->accent ? 'card__mark--distro' : '' }} {{ $logo && Str::endsWith($logo, '.png') ? 'card__mark--raster' : '' }}" aria-hidden="true">
    @if($logo && Str::endsWith($logo, '.png'))
        <img src="{{ $logo }}" alt="" class="h-full w-full object-contain">
    @elseif($logo)
        <span class="logo-glyph" style="--logo: url('{{ $logo }}')"></span>
    @else
        {{ Str::upper(Str::substr($page->title, 0, 1)) }}
    @endif
</span>
