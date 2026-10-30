@extends('layouts.app')

@section('title', 'Edit profile')

@section('content')
<div class="mx-auto max-w-md animate-fade-up motion-reduce:animate-none">
    <div class="card p-8">
        <div class="mb-6 flex items-center gap-4">
            <x-avatar :user="$user" size="lg" />
            <div>
                <h1 class="text-2xl font-bold">Edit profile</h1>
                <p class="page-lead font-mono">{{ $user->username }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update', $user->username) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="field-label" for="profile-name">Display name</label>
                <input name="name" id="profile-name" type="text" value="{{ old('name', $user->name) }}" class="input-field @error('name') input-field--invalid @enderror" required>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="profile-bio">Short bio (optional)</label>
                <textarea name="bio" id="profile-bio" rows="4" maxlength="{{ \App\Features\Auth\Models\User::BIO_MAX_LENGTH }}" class="input-field @error('bio') input-field--invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
                <p class="mt-1 text-xs text-tux-muted">Plain text, up to {{ \App\Features\Auth\Models\User::BIO_MAX_LENGTH }} characters.</p>
                @error('bio') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary">Save</button>
                <a href="{{ route('profile.show', $user->username) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
