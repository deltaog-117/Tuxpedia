<div>
    <div id="tree-container" class="tree" style="width: 100%; height: 700px;">
        <!-- D3 tree will be rendered here -->
    </div>

    <!-- Include D3.js from CDN -->
    <script src="https://d3js.org/d3.v7.min.js"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            // Wait for Livewire to render the component, then draw the tree
            Livewire.on('rendered', function () {
                drawTree();
            });
        });

        // Initial draw after page load
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(drawTree, 200);
        });

        function drawTree() {
            // Get the tree data from the blade variable
            const treeData = @json($tree);

            if (!treeData || treeData.length === 0) {
                document.getElementById('tree-container').innerHTML = '<p class="text-center text-tux-muted p-4">No distribution data available.</p>';
                return;
            }

            // Convert flat array of roots into a D3 hierarchy
            // We need to build a single root, but we have multiple roots (independent distributions).
            // We can create a virtual root node.
            const rootNode = { name: 'Linux', children: treeData };

            // Compute the tree layout
            const margin = { top: 20, right: 120, bottom: 20, left: 120 };
            const width = document.getElementById('tree-container').clientWidth - margin.left - margin.right;
            const height = document.getElementById('tree-container').clientHeight - margin.top - margin.bottom;

            // Clear previous SVG
            const container = document.getElementById('tree-container');
            container.innerHTML = '';

            // Create SVG
            const svg = d3.select(container)
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

            const root = d3.hierarchy(rootNode, function(d) {
                return d.children;
            });

            // Compute tree layout
            const treeLayout = d3.tree()
                .size([height, width]);

            const treeData2 = treeLayout(root);

            // Draw links (colours come from .tree__link so they follow the theme)
            svg.selectAll('.link')
                .data(treeData2.links())
                .enter()
                .append('path')
                .attr('class', 'link tree__link')
                .attr('d', d3.linkHorizontal()
                    .x(function(d) { return d.y; })
                    .y(function(d) { return d.x; })
                );

            // Draw nodes
            const node = svg.selectAll('.node')
                .data(treeData2.descendants())
                .enter()
                .append('g')
                .attr('class', function(d) {
                    return d.depth === 0 ? 'node tree__node tree__node--root' : 'node tree__node';
                })
                .attr('transform', function(d) {
                    return 'translate(' + d.y + ',' + d.x + ')';
                });

            // Circles for nodes
            node.append('circle')
                .attr('r', 6)
                .style('fill', function(d) {
                    return d.data.accent || null;
                });

            // Labels
            node.append('text')
                .attr('class', 'tree__label')
                .attr('dy', '.35em')
                .attr('x', function(d) {
                    return d.children ? -10 : 10;
                })
                .attr('text-anchor', function(d) {
                    return d.children ? 'end' : 'start';
                })
                .text(function(d) {
                    return d.data.name;
                });
        }
    </script>
</div>
