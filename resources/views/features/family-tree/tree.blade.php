<div class="tree-map" id="tree-map">
    <div class="tree-map__stage" wire:ignore>
        <svg id="tree-svg" class="tree-map__svg" role="img" aria-label="Map of Linux distributions and the distributions they are based on"></svg>
        <p class="tree-map__hint">Scroll to zoom, drag to pan. Select a distro for details.</p>
    </div>

    <aside class="tree-panel" id="tree-panel" aria-live="polite">
        <p class="tree-panel__empty">Select a distribution on the map.</p>
    </aside>

    <script src="https://cdn.jsdelivr.net/npm/d3@7.9.0/dist/d3.min.js"></script>
    <script>
        (function () {
            const graph = @json($graph);
            const svgEl = document.getElementById('tree-svg');
            const panel = document.getElementById('tree-panel');
            const width = svgEl.clientWidth;
            const height = svgEl.clientHeight;
            const nodes = graph.nodes.map(function (d) { return Object.assign({}, d); });
            const byId = new Map(nodes.map(function (d) { return [d.id, d]; }));
            const links = graph.links.map(function (l) { return { source: l.source, target: l.target }; });

            // Lineage = the node itself, every ancestor and every descendant.
            function lineage(d) {
                const ids = new Set([d.id]);
                for (let p = byId.get(d.parent); p; p = byId.get(p.parent)) ids.add(p.id);
                (function down(id) {
                    nodes.forEach(function (n) { if (n.parent === id) { ids.add(n.id); down(n.id); } });
                })(d.id);
                return ids;
            }

            const radius = function (d) { return 18 + 5 * Math.sqrt(d.derivatives); };

            // Independents ring the centre; each family is anchored on an ellipse around them.
            const cx = width / 2;
            const cy = height / 2;
            const families = graph.clusters.filter(function (c) { return !c.standalone; });
            const anchors = new Map(families.map(function (c, i) {
                const angle = (i / families.length) * 2 * Math.PI - Math.PI / 2;
                return [c.id, { x: cx + Math.cos(angle) * width * 0.34, y: cy + Math.sin(angle) * height * 0.4 }];
            }));
            const standalone = new Set(graph.clusters.filter(function (c) { return c.standalone; }).map(function (c) { return c.id; }));
            const inFamily = function (d) { return !standalone.has(d.cluster); };

            const svg = d3.select(svgEl);
            const world = svg.append('g');
            const zoom = d3.zoom().scaleExtent([0.4, 3]).on('zoom', function (e) { world.attr('transform', e.transform); });
            svg.call(zoom);

            const clusterLabel = world.selectAll('.tree-map__cluster').data(graph.clusters).enter()
                .append('text').attr('class', 'tree-map__cluster').attr('text-anchor', 'middle')
                .text(function (c) { return c.name; });

            const link = world.selectAll('.tree-map__link').data(links).enter()
                .append('line').attr('class', 'tree-map__link');

            const node = world.selectAll('.tree-map__node').data(nodes).enter()
                .append('g').attr('class', 'tree-map__node').attr('tabindex', 0).attr('role', 'button')
                .attr('aria-label', function (d) { return d.name; });

            const svgNs = 'http://www.w3.org/2000/svg';
            const defs = svg.append('defs');
            const maskId = function (d) { return 'logo-' + String(d.id).replace(/[^\w-]/g, '-'); };

            // Vector logos are single-colour: an alpha mask lets a white rect take their shape.
            nodes.filter(function (d) { return d.logo && !d.raster; }).forEach(function (d) {
                const r = radius(d) * 0.62;
                const mask = defs.append('mask').attr('id', maskId(d)).attr('maskUnits', 'userSpaceOnUse')
                    .attr('x', -r).attr('y', -r).attr('width', 2 * r).attr('height', 2 * r)
                    .style('mask-type', 'alpha');
                mask.append('image').attr('href', d.logo).attr('x', -r).attr('y', -r)
                    .attr('width', 2 * r).attr('height', 2 * r).attr('preserveAspectRatio', 'xMidYMid meet');
            });

            node.append('circle')
                .attr('class', function (d) { return d.raster ? 'tree-map__disc tree-map__disc--raster' : 'tree-map__disc'; })
                .attr('r', radius)
                .style('fill', function (d) { return d.raster ? '#fff' : (d.accent || null); });

            node.each(function (d) {
                const g = d3.select(this);
                const r = radius(d) * (d.raster ? 0.8 : 0.62);
                if (d.raster) {
                    g.append('image').attr('href', d.logo).attr('x', -r).attr('y', -r)
                        .attr('width', 2 * r).attr('height', 2 * r).attr('class', 'tree-map__glyph');
                } else if (d.logo) {
                    g.append('rect').attr('x', -r).attr('y', -r).attr('width', 2 * r).attr('height', 2 * r)
                        .attr('class', 'tree-map__glyph').attr('mask', 'url(#' + maskId(d) + ')');
                } else {
                    g.append('text').attr('class', 'tree-map__initial').attr('dy', '.35em').attr('text-anchor', 'middle')
                        .text(d.name.charAt(0).toUpperCase());
                }
            });

            node.append('text').attr('class', 'tree-map__name').attr('dy', function (d) { return radius(d) + 16; }).attr('text-anchor', 'middle')
                .text(function (d) { return d.name; });

            function highlight(d) {
                const ids = d ? lineage(d) : null;
                node.classed('is-dim', function (n) { return ids && !ids.has(n.id); });
                link.classed('is-dim', function (l) { return ids && !(ids.has(l.source.id) && ids.has(l.target.id)); });
            }

            function select(d) {
                node.classed('is-selected', function (n) { return n === d; });
                const parent = byId.get(d.parent);
                const kids = nodes.filter(function (n) { return n.parent === d.id; });
                panel.replaceChildren();
                const add = function (tag, cls, text) {
                    const el = document.createElement(tag);
                    el.className = cls;
                    el.textContent = text;
                    panel.appendChild(el);
                    return el;
                };
                const title = add('h2', 'tree-panel__title', d.name);
                title.style.setProperty('--tree-accent', d.accent || 'rgb(var(--tux-solid))');
                add('p', 'tree-panel__meta', parent ? 'Based on ' + parent.name : 'Root of its lineage');
                if (d.summary) add('p', 'tree-panel__text', d.summary);
                if (kids.length) add('p', 'tree-panel__meta', 'Derivatives: ' + kids.map(function (k) { return k.name; }).join(', '));
                if (d.url) {
                    const a = add('a', 'btn btn-primary tree-panel__link', 'Read the article');
                    a.href = d.url;
                } else {
                    add('p', 'tree-panel__meta', 'No article yet.');
                }
            }

            node.on('mouseenter focus', function (e, d) { highlight(d); })
                .on('mouseleave blur', function () { highlight(null); })
                .on('click', function (e, d) { select(d); })
                .on('keydown', function (e, d) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(d); } })
                .call(d3.drag()
                    .on('start', function (e) { if (!e.active) sim.alphaTarget(0.3).restart(); e.subject.fx = e.subject.x; e.subject.fy = e.subject.y; })
                    .on('drag', function (e) { e.subject.fx = e.x; e.subject.fy = e.y; })
                    .on('end', function (e) { if (!e.active) sim.alphaTarget(0); e.subject.fx = null; e.subject.fy = null; }));

            function ticked() {
                link.attr('x1', function (l) { return l.source.x; }).attr('y1', function (l) { return l.source.y; })
                    .attr('x2', function (l) { return l.target.x; }).attr('y2', function (l) { return l.target.y; });
                node.attr('transform', function (d) { return 'translate(' + d.x + ',' + d.y + ')'; });
                clusterLabel
                    .attr('x', function (c) { return d3.mean(nodes.filter(function (n) { return n.cluster === c.id; }), function (n) { return n.x; }); })
                    .attr('y', function (c) { return d3.min(nodes.filter(function (n) { return n.cluster === c.id; }), function (n) { return n.y - radius(n); }) - 22; });
            }

            const sim = d3.forceSimulation(nodes)
                .force('link', d3.forceLink(links).id(function (d) { return d.id; }).distance(70).strength(0.9))
                .force('charge', d3.forceManyBody().strength(-160))
                .force('collide', d3.forceCollide(function (d) { return radius(d) + 30; }))
                .force('x', d3.forceX(function (d) { return inFamily(d) ? anchors.get(d.cluster).x : cx; }).strength(function (d) { return inFamily(d) ? 0.14 : 0.01; }))
                .force('y', d3.forceY(function (d) { return inFamily(d) ? anchors.get(d.cluster).y : cy; }).strength(function (d) { return inFamily(d) ? 0.14 : 0.01; }))
                .force('ring', d3.forceRadial(height * 0.19, cx, cy).strength(function (d) { return inFamily(d) ? 0 : 0.9; }))
                .stop();

            // Settle before the first paint, then zoom to fit so nothing starts clipped.
            for (let i = 0; i < 300; i++) sim.tick();
            ticked();
            sim.on('tick', ticked).alpha(0.01).restart();

            const box = world.node().getBBox();
            const pad = 24;
            const scale = Math.min(1.6, (width - 2 * pad) / box.width, (height - 2 * pad) / box.height);
            svg.call(zoom.transform, d3.zoomIdentity
                .translate(width / 2 - scale * (box.x + box.width / 2), height / 2 - scale * (box.y + box.height / 2))
                .scale(scale));
        })();
    </script>
</div>
