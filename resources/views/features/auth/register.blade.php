<div class="mx-auto max-w-md animate-fade-up motion-reduce:animate-none">
    <div class="card p-8">
        <div class="mb-6 text-center">
            <span class="card__mark mx-auto mb-4" aria-hidden="true">🐧</span>
            <h2 class="text-2xl font-bold">Create your account</h2>
            <p class="page-lead">Join Tuxpedia and start editing</p>
        </div>

        <form wire:submit.prevent="register" class="space-y-4">
            <div>
                <label class="field-label" for="register-name">Name</label>
                <input wire:model="name" id="register-name" type="text" class="input-field @error('name') input-field--invalid @enderror" required>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="register-username">Username</label>
                <input wire:model="username" id="register-username" type="text" class="input-field @error('username') input-field--invalid @enderror" autocomplete="username" required>
                <p class="mt-1 text-xs text-tux-muted">Your profile address, <span class="font-mono">/user/username</span>. Lowercase letters, digits, - and _; it cannot be changed later.</p>
                @error('username') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="register-email">Email</label>
                <input wire:model="email" id="register-email" type="email" class="input-field @error('email') input-field--invalid @enderror" required>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="register-password">Password</label>
                <input wire:model="password" id="register-password" type="password" class="input-field @error('password') input-field--invalid @enderror" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="register-password-confirmation">Confirm Password</label>
                <input wire:model="password_confirmation" id="register-password-confirmation" type="password" class="input-field" required>
            </div>

            <button type="submit" class="btn-primary w-full">Register</button>
        </form>

        <p class="mt-6 text-center text-sm text-tux-muted">
            Already have an account? <a href="{{ route('auth.login') }}" class="font-medium">Login</a>
        </p>
    </div>
</div>
