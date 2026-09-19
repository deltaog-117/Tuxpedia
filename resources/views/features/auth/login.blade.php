<div class="mx-auto max-w-md animate-fade-up motion-reduce:animate-none">
    <div class="card p-8">
        <div class="mb-6 text-center">
            <span class="card__mark mx-auto mb-4" aria-hidden="true">🐧</span>
            <h2 class="text-2xl font-bold">Welcome back</h2>
            <p class="page-lead">Log in to Tuxpedia</p>
        </div>

        @if(session()->has('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        <form wire:submit.prevent="login" class="space-y-4">
            <div>
                <label class="field-label" for="login-email">Email</label>
                <input wire:model="email" id="login-email" type="email" class="input-field @error('email') input-field--invalid @enderror" required>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="login-password">Password</label>
                <input wire:model="password" id="login-password" type="password" class="input-field @error('password') input-field--invalid @enderror" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-tux-muted">
                <input wire:model="remember" type="checkbox" class="h-4 w-4 rounded accent-tux-solid">
                Remember me
            </label>

            <button type="submit" class="btn-primary w-full">Login</button>
        </form>

        <p class="mt-6 text-center text-sm text-tux-muted">
            Don't have an account? <a href="{{ route('auth.register') }}" class="font-medium">Register</a>
        </p>
    </div>
</div>
