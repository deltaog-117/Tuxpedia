@props(['user', 'size' => 'md'])

@if($user->avatarUrl())
    <img class="avatar avatar--image avatar--{{ $size }}" src="{{ $user->avatarUrl() }}" alt="" loading="lazy" decoding="async">
@else
    <span class="avatar avatar--{{ $size }}" style="--avatar-hue: {{ $user->avatarHue() }}" aria-hidden="true">{{ $user->initials() }}</span>
@endif
