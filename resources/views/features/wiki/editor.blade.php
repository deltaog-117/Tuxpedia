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
            <label class="field-label" for="kind">Page type</label>
            <select wire:model="kind" id="kind" class="input-field @error('kind') input-field--invalid @enderror">
                @foreach($kinds as $kind)
                    <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-tux-muted">Concepts appear in the glossary. Disambiguation pages list the pages an ambiguous name could mean.</p>
            @error('kind') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label" for="distribution">Distribution (optional)</label>
            <select wire:model="distributionId" id="distribution" class="input-field @error('distributionId') input-field--invalid @enderror">
                <option value="">None</option>
                @foreach($distributions as $distribution)
                    <option value="{{ $distribution->id }}">{{ $distribution->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-tux-muted">Adds the distro's colour and infobox to this page.</p>
            @error('distributionId') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label" for="categories">Categories (optional)</label>
            <input wire:model="categories" id="categories" type="text" class="input-field @error('categories') input-field--invalid @enderror" placeholder="e.g. Init system, Package manager">
            <p class="mt-1 text-xs text-tux-muted">Separate with commas. New categories are created automatically.</p>
            @error('categories') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label" for="content">Content (Markdown)</label>
            <textarea wire:model="content" id="content" rows="15" class="input-field font-mono" placeholder="Write your content in Markdown..."></textarea>
        </div>

        <div>
            <label class="field-label" for="summary">Edit summary (optional)</label>
            <input wire:model="summary" id="summary" type="text" maxlength="255" class="input-field @error('summary') input-field--invalid @enderror" placeholder="What changed and why">
            @error('summary') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="btn-primary">
                {{ $isEditing ? 'Update Page' : 'Create Page' }}
            </button>
            <a href="{{ $isEditing ? route('wiki.show', $slug) : route('wiki.index') }}" class="btn-ghost">Cancel</a>
        </div>
    </form>
</div>
