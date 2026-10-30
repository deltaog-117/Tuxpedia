@props(['user', 'size' => 'md'])

<span class="avatar avatar--{{ $size }}" style="--avatar-hue: {{ $user->avatarHue() }}" aria-hidden="true">{{ $user->initials() }}</span>
