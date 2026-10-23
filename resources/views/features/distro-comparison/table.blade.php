<div>
    <!-- Search & Filters -->
    <div class="mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="field-label" for="distro-search">Search</label>
            <input wire:model.live="search" id="distro-search" type="text" placeholder="Search distros..." class="input-field">
        </div>
        <div>
            <label class="field-label" for="distro-based-on">Based On</label>
            <select wire:model.live="filters.based_on" id="distro-based-on" class="input-field w-auto">
                <option value="">All</option>
                @foreach($basedOnOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="distro-release-model">Release Model</label>
            <select wire:model.live="filters.release_model" id="distro-release-model" class="input-field w-auto">
                <option value="">All</option>
                @foreach($releaseModelOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button wire:click="resetFilters" class="btn-secondary">Reset</button>
        </div>
    </div>

    <!-- Table -->
    <div class="data-table__wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col" wire:click="sortBy('name')" class="data-table__sortable">Name</th>
                    <th scope="col" wire:click="sortBy('based_on')" class="data-table__sortable">Based On</th>
                    <th scope="col" wire:click="sortBy('package_manager')" class="data-table__sortable">Package Manager</th>
                    <th scope="col" wire:click="sortBy('default_desktop')" class="data-table__sortable">Default Desktop</th>
                    <th scope="col" wire:click="sortBy('release_model')" class="data-table__sortable">Release Model</th>
                    <th scope="col" wire:click="sortBy('architecture')" class="data-table__sortable">Architecture</th>
                    <th scope="col">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributions as $distro)
                    <tr>
                        <td class="font-semibold" style="{{ $distro->accentStyle() }}">
                            <span class="distro-dot" aria-hidden="true"></span>{{ $distro->name }}
                        </td>
                        <td>
                            @if($distro->based_on)
                                <span class="chip">{{ $distro->based_on }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="font-mono text-xs">{{ $distro->package_manager ?? '—' }}</td>
                        <td>{{ $distro->default_desktop ?? '—' }}</td>
                        <td>{{ $distro->release_model ?? '—' }}</td>
                        <td class="font-mono text-xs">{{ $distro->architecture ?? '—' }}</td>
                        <td class="text-tux-muted">{{ Str::limit($distro->description, 60) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-tux-muted">No distributions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $distributions->links() }}
    </div>
</div>
