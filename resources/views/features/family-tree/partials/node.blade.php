<li @if($node['accent']) style="--tree-accent: {{ $node['accent'] }}" @endif>
    @include('features.family-tree.partials.chip', ['node' => $node])
    @if($node['children'] !== [])
        <ul class="tree-list">
            @foreach($node['children'] as $child)
                @include('features.family-tree.partials.node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
