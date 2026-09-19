<div class="card p-6 sm:p-8">
    @if(session()->has('message'))
        <div class="alert alert--success">
            {{ session('message') }}
        </div>
    @endif

    @if($errorMessage)
        <div class="alert alert--error">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <div>
            <label class="field-label" for="slug">Slug (URL identifier)</label>
            <input wire:model="slug" id="slug" type="text" class="input-field font-mono @error('slug') input-field--invalid @enderror" placeholder="e.g. ubuntu-linux">
            @error('slug') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label" for="title">Title</label>
            <input wire:model="title" id="title" type="text" class="input-field @error('title') input-field--invalid @enderror" placeholder="Page title">
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label" for="author">Author (optional)</label>
            <input wire:model="author" id="author" type="text" class="input-field" placeholder="Your name">
        </div>

        <div>
            <label class="field-label" for="content">Content (Markdown)</label>
            <textarea wire:model="content" id="content" rows="15" class="input-field font-mono" placeholder="Write your content in Markdown..."></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="btn-primary">
                {{ $isEditing ? 'Update Page' : 'Create Page' }}
            </button>
            <a href="{{ $isEditing ? route('wiki.show', $slug) : route('wiki.index') }}" class="btn-ghost">Cancel</a>
        </div>
    </form>
</div>
