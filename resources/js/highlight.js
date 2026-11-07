// Bundled to public/js/highlight.js by `npm run js:build`.
// Only fenced blocks that name a language are coloured: guessing the language
// of a bare block would mis-colour plain output such as a command's result.
import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import c from 'highlight.js/lib/languages/c';
import css from 'highlight.js/lib/languages/css';
import diff from 'highlight.js/lib/languages/diff';
import dockerfile from 'highlight.js/lib/languages/dockerfile';
import ini from 'highlight.js/lib/languages/ini';
import javascript from 'highlight.js/lib/languages/javascript';
import json from 'highlight.js/lib/languages/json';
import lua from 'highlight.js/lib/languages/lua';
import makefile from 'highlight.js/lib/languages/makefile';
import nginx from 'highlight.js/lib/languages/nginx';
import php from 'highlight.js/lib/languages/php';
import python from 'highlight.js/lib/languages/python';
import shell from 'highlight.js/lib/languages/shell';
import xml from 'highlight.js/lib/languages/xml';
import yaml from 'highlight.js/lib/languages/yaml';

hljs.registerLanguage('bash', bash);
hljs.registerLanguage('c', c);
hljs.registerLanguage('css', css);
hljs.registerLanguage('diff', diff);
hljs.registerLanguage('dockerfile', dockerfile);
hljs.registerLanguage('ini', ini);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('json', json);
hljs.registerLanguage('lua', lua);
hljs.registerLanguage('makefile', makefile);
hljs.registerLanguage('nginx', nginx);
hljs.registerLanguage('php', php);
hljs.registerLanguage('python', python);
hljs.registerLanguage('shell', shell);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('yaml', yaml);

// Names people commonly write in a fence that differ from highlight.js's own.

document.querySelectorAll('.article pre code[class*="language-"]').forEach((block) => {
    const match = /(?:^|\s)language-([\w+-]+)/.exec(block.className);
    if (!match) { return; }
    const name = ALIASES[match[1].toLowerCase()] ?? match[1].toLowerCase();
    if (!hljs.getLanguage(name)) { return; }
    // highlight() escapes its input, so the wiki's sanitised text cannot gain markup.
    block.innerHTML = hljs.highlight(block.textContent, { language: name, ignoreIllegals: true }).value;
    block.classList.add('hljs');
});
