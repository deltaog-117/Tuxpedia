@props(['name' => null, 'user' => null])

{{-- A linked account shows its current display name; older rows without one keep the stored text. --}}
@if($user)
    <a href="{{ route('profile.show', $user->username) }}">{{ $user->name }}</a>
@else
    {{ $name ?? '—' }}
@endif
