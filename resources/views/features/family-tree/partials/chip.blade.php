<{{ $node['page'] ? 'a href='.route('wiki.show', $node['page']) : 'span' }} class="tree-chip" @if($node['accent']) style="--tree-accent: {{ $node['accent'] }}" @endif>
    <span class="tree-chip__dot"></span>{{ $node['name'] }}
</{{ $node['page'] ? 'a' : 'span' }}>
