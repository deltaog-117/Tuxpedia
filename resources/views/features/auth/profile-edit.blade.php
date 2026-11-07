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

        <form method="POST" action="{{ route('profile.update', $user->username) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <label class="field-label" for="profile-avatar">Profile picture (optional)</label>
                <input name="avatar" id="profile-avatar" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="input-field @error('avatar') input-field--invalid @enderror" data-avatar-input>
                <p class="mt-1 text-xs text-tux-muted">Your browser crops it to a square and shrinks it to 256 pixels before upload. Without JavaScript, send a square JPEG or WebP under {{ intdiv(\App\Features\Auth\ValueObjects\AvatarImage::MAX_BYTES, 1000) }} KB.</p>
                @error('avatar') <p class="field-error">{{ $message }}</p> @enderror
                @if($user->avatarUrl())
                    <label class="mt-2 flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remove_avatar" value="1"> Remove my current picture
                    </label>
                @endif
            </div>

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

@push('scripts')
<script>
    // Crops the chosen picture to a centred square, scales it to 256 px and re-encodes it
    // (WebP, else JPEG), which also drops EXIF data such as GPS position. The server
    // re-checks the file, so this step is for the person's benefit, not a security gate.
    (function () {
        var input = document.querySelector('[data-avatar-input]');
        if (!input || !window.DataTransfer || !HTMLCanvasElement.prototype.toBlob) { return; }

        var SIDE = 256;
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) { return; }

            var url = URL.createObjectURL(file);
            var image = new Image();
            image.onload = function () {
                URL.revokeObjectURL(url);
                var edge = Math.min(image.naturalWidth, image.naturalHeight);
                var canvas = document.createElement('canvas');
                canvas.width = SIDE;
                canvas.height = SIDE;
                canvas.getContext('2d').drawImage(
                    image,
                    (image.naturalWidth - edge) / 2, (image.naturalHeight - edge) / 2, edge, edge,
                    0, 0, SIDE, SIDE
                );

                var done = function (blob, name, type) {
                    var transfer = new DataTransfer();
                    transfer.items.add(new File([blob], name, { type: type }));
                    input.files = transfer.files;
                };
                canvas.toBlob(function (webp) {
                    if (webp && webp.type === 'image/webp') { return done(webp, 'avatar.webp', 'image/webp'); }
                    canvas.toBlob(function (jpeg) { done(jpeg, 'avatar.jpg', 'image/jpeg'); }, 'image/jpeg', 0.85);
                }, 'image/webp', 0.85);
            };
            image.onerror = function () { URL.revokeObjectURL(url); };
            image.src = url;
        });
    })();
</script>
@endpush
