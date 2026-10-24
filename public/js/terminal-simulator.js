/**
 * Tuxpedia Terminal Simulator
 * A lightweight, mock Linux terminal that runs entirely in the browser.
 */

// ===== Virtual Filesystem =====
const virtualFS = {
    '/': {
        type: 'dir',
        children: {
            'home': { type: 'dir', children: {
                'user': { type: 'dir', children: {
                    'Documents': { type: 'dir', children: {
                        'notes.txt': { type: 'file', content: 'This is a sample document.' }
                    } },
                    'Downloads': { type: 'dir', children: {} },
                    '.bashrc': { type: 'file', content: 'alias ll="ls -la"\nexport PS1="\\u@tuxpedia:\\w$ "' }
                } }
            } },
            'etc': { type: 'dir', children: {
                'os-release': { type: 'file', content: 'NAME="Tuxpedia Linux"\nVERSION="0.1.0"\nID=tuxpedia\nPRETTY_NAME="Tuxpedia Linux 0.1.0"' }
            } },
            'bin': { type: 'dir', children: {} },
            'usr': { type: 'dir', children: {} },
            'var': { type: 'dir', children: {} },
            'tmp': { type: 'dir', children: {} },
            'root': { type: 'dir', children: {} },
            'README.md': { type: 'file', content: '# Welcome to Tuxpedia\n\nThis is a simulated Linux environment.' },
            'hello.txt': { type: 'file', content: 'Hello from Tuxpedia!' },
        }
    }
};

// ===== Utility Functions =====
const HOME = '/home/user';

function resolvePath(path, cwd) {
    if (path === '~' || path.startsWith('~/')) {
        path = HOME + path.slice(1);
    }
    const base = path.startsWith('/') ? path : cwd + '/' + path;
    const out = [];
    for (const part of base.split('/')) {
        if (part === '' || part === '.') {
            continue;
        }
        if (part === '..') {
            out.pop();
        } else {
            out.push(part);
        }
    }
    return '/' + out.join('/');
}

function getNode(path, fs) {
    if (path === '/') {
        return fs['/'];
    }
    const parts = path.split('/').filter(p => p !== '');
    let current = fs['/'];
    for (const part of parts) {
        if (!current.children || !current.children[part]) {
            return null;
        }
        current = current.children[part];
    }
    return current;
}

function getParentPath(path) {
    const parts = path.split('/').filter(p => p !== '');
    parts.pop();
    return '/' + parts.join('/');
}

function baseName(path) {
    return path.split('/').filter(p => p !== '').pop() || '';
}

function promptFor(cwd) {
    let shown = cwd;
    if (cwd === HOME) {
        shown = '~';
    } else if (cwd.startsWith(HOME + '/')) {
        shown = '~' + cwd.slice(HOME.length);
    }
    return `user@tuxpedia:${shown}$ `;
}

// Splits "-la file" into { flags: Set('l','a'), rest: ['file'] }.
function parseArgs(args) {
    const flags = new Set();
    const rest = [];
    for (const arg of args) {
        if (arg.length > 1 && arg.startsWith('-') && !/^-\d+$/.test(arg)) {
            for (const ch of arg.slice(1)) {
                flags.add(ch);
            }
        } else {
            rest.push(arg);
        }
    }
    return { flags, rest };
}

function sizeOf(node) {
    return node.type === 'dir' ? 4096 : (node.content || '').length;
}

function readLines(cmd, args, env, stdin) {
    const { flags, rest } = parseArgs(args);
    let count = 10;
    const nIdx = args.indexOf('-n');
    if (nIdx !== -1 && args[nIdx + 1]) {
        count = parseInt(args[nIdx + 1], 10) || 10;
        rest.splice(rest.indexOf(args[nIdx + 1]), 1);
    }
    const target = rest[0];
    if (!target && stdin !== undefined) {
        return { lines: stdin.split('\n'), count };
    }
    if (!target) {
        return { error: `${cmd}: missing file operand` };
    }
    const node = getNode(resolvePath(target, env.cwd), virtualFS);
    if (!node) {
        return { error: `${cmd}: ${target}: No such file or directory` };
    }
    if (node.type !== 'file') {
        return { error: `${cmd}: ${target}: Is a directory` };
    }
    return { lines: (node.content || '').split('\n'), count };
}

const MAN_PAGES = {
    ls: 'list directory contents. Flags: -a (hidden files), -l (long format).',
    cd: 'change the working directory. "cd" alone or "cd ~" goes home.',
    pwd: 'print the current working directory.',
    cat: 'print the contents of one or more files.',
    echo: 'print its arguments.',
    mkdir: 'create directories.',
    touch: 'create empty files.',
    rm: 'remove files. Use -r for directories.',
    cp: 'copy files. Use -r for directories.',
    mv: 'move or rename files.',
    tree: 'show a directory as an indented tree.',
    head: 'print the first lines of a file. Flag: -n N.',
    tail: 'print the last lines of a file. Flag: -n N.',
    wc: 'count lines, words and bytes in a file.',
    grep: 'print lines of a file that match a pattern. Flags: -i, -n.',
    date: 'print the current date and time.',
    uname: 'print system information.',
    whoami: 'print the current user name.',
};

const FORTUNES = [
    'There are only 10 kinds of people: those who understand binary and those who do not.',
    'Everything is a file.',
    'rm -rf is forever. Think twice, type once.',
    'sudo make me a sandwich.',
    'It works on my machine.',
    'Linux is only free if your time has no value.',
];

// Splits a command line into words and { op } tokens for | > >>.
// Quotes group words; operators inside quotes stay literal.
function tokenize(line) {
    const tokens = [];
    let cur = '';
    let started = false;
    let quote = null;
    const flush = () => {
        if (started) {
            tokens.push(cur);
        }
        cur = '';
        started = false;
    };
    for (let i = 0; i < line.length; i++) {
        const ch = line[i];
        if (quote) {
            if (ch === quote) {
                quote = null;
            } else {
                cur += ch;
            }
        } else if (ch === '"' || ch === "'") {
            quote = ch;
            started = true;
        } else if (/\s/.test(ch)) {
            flush();
        } else if (ch === '|') {
            flush();
            tokens.push({ op: '|' });
        } else if (ch === '>') {
            flush();
            if (line[i + 1] === '>') {
                i++;
                tokens.push({ op: '>>' });
            } else {
                tokens.push({ op: '>' });
            }
        } else {
            cur += ch;
            started = true;
        }
    }
    flush();
    return tokens;
}

// Candidates for Tab: command names for the first word, paths otherwise.
function completionsFor(input, env, commandNames) {
    const word = input.split(/\s/).pop();
    const isFirst = !/\s/.test(input.replace(/^\s+/, ''));
    if (isFirst) {
        return { word, matches: commandNames.filter(n => n.startsWith(word)).map(n => n + ' ') };
    }
    const slash = word.lastIndexOf('/');
    const dirPart = slash === -1 ? '' : word.slice(0, slash + 1);
    const prefix = word.slice(slash + 1);
    const dir = getNode(resolvePath(dirPart || '.', env.cwd), virtualFS);
    if (!dir || dir.type !== 'dir') {
        return { word, matches: [] };
    }
    const matches = Object.keys(dir.children)
        .filter(n => n.startsWith(prefix) && (prefix.startsWith('.') || !n.startsWith('.')))
        .sort()
        .map(n => dirPart + n + (dir.children[n].type === 'dir' ? '/' : ' '));
    return { word, matches };
}

function commonPrefix(items) {
    let prefix = items[0];
    for (const item of items) {
        while (!item.startsWith(prefix)) {
            prefix = prefix.slice(0, -1);
        }
    }
    return prefix;
}

// ===== Command Handlers =====
const commands = {
    ls: function(args, env) {
        const { flags, rest } = parseArgs(args);
        const target = rest[0] || '.';
        const node = getNode(resolvePath(target, env.cwd), virtualFS);
        if (!node) {
            return `ls: cannot access '${target}': No such file or directory`;
        }
        if (node.type !== 'dir') {
            return target;
        }
        const names = Object.keys(node.children || {})
            .filter(name => flags.has('a') || !name.startsWith('.'))
            .sort();
        if (flags.has('l')) {
            return names.map(name => {
                const child = node.children[name];
                const mode = child.type === 'dir' ? 'drwxr-xr-x' : '-rw-r--r--';
                return `${mode} user user ${String(sizeOf(child)).padStart(5)} ${name}`;
            }).join('\n');
        }
        return names.join(env.piped ? '\n' : '  ');
    },

    pwd: function(args, env) {
        return env.cwd;
    },

    cd: function(args, env) {
        const target = args[0] || HOME;
        const resolved = resolvePath(target, env.cwd);
        const node = getNode(resolved, virtualFS);
        if (!node || node.type !== 'dir') {
            return `cd: ${target}: No such file or directory`;
        }
        env.cwd = resolved;
        return '';
    },

    echo: function(args, env) {
        return args.join(' ');
    },

    cat: function(args, env, stdin) {
        if (args.length === 0 && stdin !== undefined) {
            return stdin;
        }
        if (args.length === 0) {
            return 'cat: missing file operand';
        }
        return args.map(target => {
            const node = getNode(resolvePath(target, env.cwd), virtualFS);
            if (!node) {
                return `cat: ${target}: No such file or directory`;
            }
            if (node.type !== 'file') {
                return `cat: ${target}: Is a directory`;
            }
            return node.content || '';
        }).join('\n');
    },

    mkdir: function(args, env) {
        const { rest } = parseArgs(args);
        if (rest.length === 0) {
            return 'mkdir: missing operand';
        }
        return rest.map(target => {
            const path = resolvePath(target, env.cwd);
            const parent = getNode(getParentPath(path), virtualFS);
            if (!parent || parent.type !== 'dir') {
                return `mkdir: cannot create directory '${target}': No such file or directory`;
            }
            if (parent.children[baseName(path)]) {
                return `mkdir: cannot create directory '${target}': File exists`;
            }
            parent.children[baseName(path)] = { type: 'dir', children: {} };
            return '';
        }).filter(Boolean).join('\n');
    },

    touch: function(args, env) {
        if (args.length === 0) {
            return 'touch: missing file operand';
        }
        return args.map(target => {
            const path = resolvePath(target, env.cwd);
            const parent = getNode(getParentPath(path), virtualFS);
            if (!parent || parent.type !== 'dir') {
                return `touch: cannot touch '${target}': No such file or directory`;
            }
            if (!parent.children[baseName(path)]) {
                parent.children[baseName(path)] = { type: 'file', content: '' };
            }
            return '';
        }).filter(Boolean).join('\n');
    },

    rm: function(args, env) {
        const { flags, rest } = parseArgs(args);
        if (rest.length === 0) {
            return 'rm: missing operand';
        }
        return rest.map(target => {
            const path = resolvePath(target, env.cwd);
            const node = getNode(path, virtualFS);
            if (path === '/' || path === env.cwd || env.cwd.startsWith(path + '/')) {
                return `rm: refusing to remove '${target}'`;
            }
            if (!node) {
                return `rm: cannot remove '${target}': No such file or directory`;
            }
            if (node.type === 'dir' && !flags.has('r') && !flags.has('R')) {
                return `rm: cannot remove '${target}': Is a directory`;
            }
            delete getNode(getParentPath(path), virtualFS).children[baseName(path)];
            return '';
        }).filter(Boolean).join('\n');
    },

    cp: function(args, env) {
        const { flags, rest } = parseArgs(args);
        if (rest.length < 2) {
            return 'cp: missing destination file operand';
        }
        const [src, dst] = rest;
        const node = getNode(resolvePath(src, env.cwd), virtualFS);
        if (!node) {
            return `cp: cannot stat '${src}': No such file or directory`;
        }
        if (node.type === 'dir' && !flags.has('r') && !flags.has('R')) {
            return `cp: -r not specified; omitting directory '${src}'`;
        }
        let dstPath = resolvePath(dst, env.cwd);
        const dstNode = getNode(dstPath, virtualFS);
        if (dstNode && dstNode.type === 'dir') {
            dstPath = dstPath + '/' + baseName(src);
        }
        const parent = getNode(getParentPath(dstPath), virtualFS);
        if (!parent || parent.type !== 'dir') {
            return `cp: cannot create '${dst}': No such file or directory`;
        }
        parent.children[baseName(dstPath)] = structuredClone(node);
        return '';
    },

    mv: function(args, env) {
        if (args.length < 2) {
            return 'mv: missing destination file operand';
        }
        const [src, dst] = args;
        const srcPath = resolvePath(src, env.cwd);
        const node = getNode(srcPath, virtualFS);
        if (!node || srcPath === '/') {
            return `mv: cannot stat '${src}': No such file or directory`;
        }
        let dstPath = resolvePath(dst, env.cwd);
        const dstNode = getNode(dstPath, virtualFS);
        if (dstNode && dstNode.type === 'dir') {
            dstPath = dstPath + '/' + baseName(src);
        }
        const parent = getNode(getParentPath(dstPath), virtualFS);
        if (!parent || parent.type !== 'dir' || dstPath.startsWith(srcPath + '/')) {
            return `mv: cannot move '${src}' to '${dst}': No such file or directory`;
        }
        delete getNode(getParentPath(srcPath), virtualFS).children[baseName(srcPath)];
        parent.children[baseName(dstPath)] = node;
        return '';
    },

    tree: function(args, env) {
        const target = args[0] || '.';
        const node = getNode(resolvePath(target, env.cwd), virtualFS);
        if (!node) {
            return `${target} [error opening dir]`;
        }
        const lines = [target];
        const walk = (dir, prefix) => {
            const names = Object.keys(dir.children).filter(n => !n.startsWith('.')).sort();
            names.forEach((name, i) => {
                const last = i === names.length - 1;
                lines.push(prefix + (last ? '└── ' : '├── ') + name);
                if (dir.children[name].type === 'dir') {
                    walk(dir.children[name], prefix + (last ? '    ' : '│   '));
                }
            });
        };
        if (node.type === 'dir') {
            walk(node, '');
        }
        return lines.join('\n');
    },

    head: function(args, env, stdin) {
        const r = readLines('head', args, env, stdin);
        return r.error || r.lines.slice(0, r.count).join('\n');
    },

    tail: function(args, env, stdin) {
        const r = readLines('tail', args, env, stdin);
        return r.error || r.lines.slice(-r.count).join('\n');
    },

    wc: function(args, env, stdin) {
        const target = args[0];
        let text;
        if (!target && stdin !== undefined) {
            text = stdin;
        } else if (!target) {
            return 'wc: missing file operand';
        } else {
            const node = getNode(resolvePath(target, env.cwd), virtualFS);
            if (!node) {
                return `wc: ${target}: No such file or directory`;
            }
            if (node.type !== 'file') {
                return `wc: ${target}: Is a directory`;
            }
            text = node.content || '';
        }
        const lines = text === '' ? 0 : text.split('\n').length;
        const words = text.split(/\s+/).filter(Boolean).length;
        return `${lines} ${words} ${text.length}${target ? ' ' + target : ''}`;
    },

    sort: function(args, env, stdin) {
        const r = readLines('sort', args, env, stdin);
        if (r.error) {
            return r.error;
        }
        const sorted = r.lines.slice().sort();
        return (args.includes('-r') ? sorted.reverse() : sorted).join('\n');
    },

    uniq: function(args, env, stdin) {
        const r = readLines('uniq', args, env, stdin);
        if (r.error) {
            return r.error;
        }
        return r.lines.filter((line, i) => i === 0 || line !== r.lines[i - 1]).join('\n');
    },

    grep: function(args, env, stdin) {
        const { flags, rest } = parseArgs(args);
        if (rest.length < 2 && !(rest.length === 1 && stdin !== undefined)) {
            return 'Usage: grep [-i] [-n] PATTERN [FILE]';
        }
        const [pattern, target] = rest;
        let text = stdin;
        if (target) {
            const node = getNode(resolvePath(target, env.cwd), virtualFS);
            if (!node) {
                return `grep: ${target}: No such file or directory`;
            }
            if (node.type !== 'file') {
                return `grep: ${target}: Is a directory`;
            }
            text = node.content || '';
        }
        const needle = flags.has('i') ? pattern.toLowerCase() : pattern;
        return text.split('\n')
            .map((line, i) => ({ line, n: i + 1 }))
            .filter(({ line }) => (flags.has('i') ? line.toLowerCase() : line).includes(needle))
            .map(({ line, n }) => (flags.has('n') ? `${n}:${line}` : line))
            .join('\n');
    },

    whoami: function(args, env) {
        return 'user';
    },

    id: function(args, env) {
        return 'uid=1000(user) gid=1000(user) groups=1000(user),27(sudo)';
    },

    hostname: function(args, env) {
        return 'tuxpedia';
    },

    uname: function(args, env) {
        if (args.includes('-r')) {
            return '6.1.0';
        }
        return 'Linux tuxpedia 6.1.0 #1 SMP PREEMPT_DYNAMIC x86_64 GNU/Linux';
    },

    date: function(args, env) {
        return new Date().toString();
    },

    uptime: function(args, env) {
        const mins = Math.floor((Date.now() - env.startedAt) / 60000);
        return ` up ${mins} min,  1 user,  load average: 0.00, 0.01, 0.05`;
    },

    free: function(args, env) {
        return [
            '               total        used        free      shared  buff/cache   available',
            'Mem:         8049124     1534212     4802316       81204     1712596     6221048',
            'Swap:        2097148           0     2097148',
        ].join('\n');
    },

    df: function(args, env) {
        return [
            'Filesystem      Size  Used Avail Use% Mounted on',
            '/dev/sda1        50G   18G   30G  38% /',
            'tmpfs           3.9G     0  3.9G   0% /tmp',
        ].join('\n');
    },

    env: function(args, env) {
        return [
            'USER=user',
            `HOME=${HOME}`,
            `PWD=${env.cwd}`,
            'SHELL=/bin/bash',
            'HOSTNAME=tuxpedia',
            'LANG=en_US.UTF-8',
        ].join('\n');
    },

    history: function(args, env) {
        return env.history.map((line, i) => `${String(i + 1).padStart(4)}  ${line}`).join('\n');
    },

    ps: function(args, env) {
        return [
            '    PID TTY          TIME CMD',
            '      1 pts/0    00:00:00 bash',
            '     42 pts/0    00:00:00 ps',
        ].join('\n');
    },

    neofetch: function(args, env) {
        return [
            '    .--.       user@tuxpedia',
            '   |o_o |      -------------',
            '   |:_/ |      OS: Tuxpedia Linux 0.1.0',
            '  //   \\ \\     Kernel: 6.1.0',
            ' (|     | )    Shell: bash',
            "/'\\_   _/`\\    Terminal: Tuxpedia Terminal",
            '\\___)=(___/    Memory: 1498MiB / 7860MiB',
        ].join('\n');
    },

    which: function(args, env) {
        if (args.length === 0) {
            return '';
        }
        return args.map(name => (commands[name] ? `/usr/bin/${name}` : `${name} not found`)).join('\n');
    },

    man: function(args, env) {
        if (args.length === 0) {
            return 'What manual page do you want?';
        }
        const page = MAN_PAGES[args[0]];
        return page ? `${args[0].toUpperCase()}(1)\n\n  ${args[0]} - ${page}` : `No manual entry for ${args[0]}`;
    },

    cowsay: function(args, env) {
        const text = args.join(' ') || 'Moo!';
        const bar = '-'.repeat(text.length + 2);
        return [
            ` ${bar}`,
            `< ${text} >`,
            ` ${bar}`,
            '        \\   ^__^',
            '         \\  (oo)\\_______',
            '            (__)\\       )\\/\\',
            '                ||----w |',
            '                ||     ||',
        ].join('\n');
    },

    fortune: function(args, env) {
        return FORTUNES[Math.floor(Math.random() * FORTUNES.length)];
    },

    sudo: function(args, env) {
        return 'user is not in the sudoers file. This incident will be reported.';
    },

    clear: function(args, env) {
        return 'CLEAR_SCREEN';
    },

    exit: function(args, env) {
        return 'EXIT';
    },

    help: function(args, env) {
        return `
Available commands:
  ls [-a] [-l] [path]  - List directory contents
  cd [path]            - Change directory
  pwd                  - Print working directory
  tree [path]          - Show a directory tree
  cat [file...]        - Display file contents
  head/tail [-n N] f   - First/last lines of a file
  wc [file]            - Count lines, words, bytes
  grep [-in] pat [f]   - Search a file or piped text
  sort [-r], uniq      - Sort lines / drop repeated lines
  mkdir, touch         - Create directories / files
  cp [-r], mv, rm [-r] - Copy, move, remove
  echo [text]          - Display text
  whoami, id, hostname - Who and where you are
  uname, date, uptime  - System information
  free, df, ps, env    - Resources and processes
  neofetch             - System summary
  history              - Command history
  which, man [cmd]     - Command info
  cowsay, fortune      - Just for fun
  clear, exit, help

Shell features:
  cmd1 | cmd2          - Pipe output into the next command
  cmd > file, >> file  - Write or append output to a file
  "quoted text"        - Keep spaces inside one argument
  Tab                  - Complete commands and paths
`.trim();
    }
};

// ===== Terminal Initialization =====
function initTerminal(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Terminal container not found.');
        return;
    }

    // Check if terminal already initialized
    if (container._terminal) {
        return;
    }

    const term = new Terminal({
        cursorBlink: true,
        theme: {
            background: '#1e1e1e',
            foreground: '#d4d4d4',
            cursor: '#ffffff',
            selection: '#264f78',
            black: '#000000',
            red: '#cd3131',
            green: '#0dbc79',
            yellow: '#e5e510',
            blue: '#2472c8',
            magenta: '#bc3fbc',
            cyan: '#11a8cd',
            white: '#e5e5e5',
            brightBlack: '#666666',
            brightRed: '#f14c4c',
            brightGreen: '#23d18b',
            brightYellow: '#f5f543',
            brightBlue: '#3b8eea',
            brightMagenta: '#d670d6',
            brightCyan: '#29b8db',
            brightWhite: '#e5e5e5'
        },
        fontFamily: '"JetBrains Mono", "Fira Code", "DejaVu Sans Mono", Menlo, Consolas, monospace',
        fontSize: 14,
        lineHeight: 1.25,
        cursorStyle: 'block',
        scrollback: 1000,
    });

    const fitAddon = new FitAddon.FitAddon();
    term.loadAddon(fitAddon);

    term.open(container);
    fitAddon.fit();

    // Save reference to prevent double init
    container._terminal = term;

    // ===== Terminal State =====
    const env = {
        cwd: HOME,
        history: [],
        startedAt: Date.now()
    };

    let currentInput = '';
    const history = env.history;
    const windowEl = container.closest('.terminal-window');
    const titleEl = windowEl && windowEl.querySelector('.terminal-window__title');
    let historyIndex = -1;
    let isExited = false;

    // ===== Input Handling =====
    function writePrompt() {
        term.write('\r\n' + promptFor(env.cwd));
        if (titleEl) {
            titleEl.textContent = 'guest@tuxpedia: ' + promptFor(env.cwd).slice('user@tuxpedia:'.length, -2);
        }
    }

    function endSession() {
        if (isExited) {
            return;
        }
        isExited = true;
        term.write('\r\nGoodbye! 🐧');
        setTimeout(() => {
            term.dispose();
            container._terminal = null;
            container.innerHTML = '<p class="text-center text-gray-400 p-4">Terminal session ended. <button onclick="location.reload()" class="text-blue-500 hover:underline">Reload</button></p>';
        }, 1000);
    }

    function writeRedirect(target, text, append, env) {
        const path = resolvePath(target, env.cwd);
        const parent = getNode(getParentPath(path), virtualFS);
        if (!parent || parent.type !== 'dir') {
            return `bash: ${target}: No such file or directory`;
        }
        const existing = parent.children[baseName(path)];
        if (existing && existing.type === 'dir') {
            return `bash: ${target}: Is a directory`;
        }
        const old = existing && append && existing.content ? existing.content + '\n' : '';
        parent.children[baseName(path)] = { type: 'file', content: old + text };
        return '';
    }

    function runSegment(tokens, stdin, isLast) {
        let redirect = null;
        const words = [];
        for (let i = 0; i < tokens.length; i++) {
            const t = tokens[i];
            if (typeof t === 'string') {
                words.push(t);
            } else if (t.op === '>' || t.op === '>>') {
                if (typeof tokens[i + 1] !== 'string') {
                    return { error: "bash: syntax error near unexpected token `newline'" };
                }
                redirect = { target: tokens[++i], append: t.op === '>>' };
            }
        }
        if (words.length === 0) {
            return { error: "bash: syntax error near unexpected token `|'" };
        }
        const [cmd, ...args] = words;
        if (cmd === 'exit') {
            endSession();
            return { output: '' };
        }
        if (!commands[cmd]) {
            return { error: `bash: ${cmd}: command not found` };
        }
        env.piped = !isLast || redirect !== null;
        const output = commands[cmd](args, env, stdin);
        if (output === 'CLEAR_SCREEN') {
            term.clear();
            return { output: '' };
        }
        if (redirect) {
            return { output: writeRedirect(redirect.target, output, redirect.append, env) };
        }
        return { output };
    }

    function processCommand(input) {
        if (input.trim() === '') {
            return '';
        }

        const segments = [[]];
        for (const t of tokenize(input)) {
            if (typeof t !== 'string' && t.op === '|') {
                segments.push([]);
            } else {
                segments[segments.length - 1].push(t);
            }
        }

        let stdin;
        for (const [i, tokens] of segments.entries()) {
            const result = runSegment(tokens, stdin, i === segments.length - 1);
            if (result.error) {
                return result.error;
            }
            stdin = result.output;
        }
        return stdin;
    }

    function redrawInput() {
        term.write('\r\x1b[K' + promptFor(env.cwd) + currentInput);
    }

    function completeInput() {
        const { word, matches } = completionsFor(currentInput, env, Object.keys(commands));
        if (matches.length === 0) {
            return;
        }
        if (matches.length === 1) {
            const addition = matches[0].slice(word.length);
            currentInput += addition;
            term.write(addition);
            return;
        }
        const shared = commonPrefix(matches);
        if (shared.length > word.length) {
            const addition = shared.slice(word.length);
            currentInput += addition;
            term.write(addition);
            return;
        }
        term.write('\r\n' + matches.map(m => m.trimEnd()).join('  ') + '\r\n');
        redrawInput();
    }

    // ===== Key Events =====
    term.onKey((e) => {
        const key = e.key;
        const ev = e.domEvent;

        if (isExited) return;

        if (ev.key === 'Enter') {
            term.write('\r\n');
            const output = processCommand(currentInput);
            if (output !== '') {
                term.writeln(output.replace(/\r?\n/g, '\r\n'));
            }
            if (!isExited) {
                writePrompt();
            }
            if (currentInput.trim() !== '' && currentInput.trim() !== 'exit') {
                history.push(currentInput);
            }
            historyIndex = history.length;
            currentInput = '';
            return;
        }

        if (ev.key === 'Tab') {
            ev.preventDefault();
            completeInput();
            return;
        }

        if (ev.key === 'Backspace') {
            if (currentInput.length > 0) {
                currentInput = currentInput.slice(0, -1);
                term.write('\b \b');
            }
            return;
        }

        if (ev.key === 'ArrowUp') {
            if (history.length > 0) {
                if (historyIndex > 0) {
                    historyIndex--;
                }
                const prev = history[historyIndex];
                if (prev !== undefined) {
                    // Clear current input
                    term.write('\b \b'.repeat(currentInput.length));
                    currentInput = prev;
                    term.write(prev);
                }
            }
            return;
        }

        if (ev.key === 'ArrowDown') {
            if (historyIndex < history.length - 1) {
                historyIndex++;
                const next = history[historyIndex];
                if (next !== undefined) {
                    term.write('\b \b'.repeat(currentInput.length));
                    currentInput = next;
                    term.write(next);
                }
            } else {
                historyIndex = history.length;
                term.write('\b \b'.repeat(currentInput.length));
                currentInput = '';
            }
            return;
        }

        // Printable characters
        if (key.length === 1 && key.charCodeAt(0) >= 32) {
            currentInput += key;
            term.write(key);
        }
    });

    // Initial prompt
    term.writeln('Welcome to Tuxpedia Terminal Simulator!');
    term.writeln('Type `help` for a list of commands.');
    writePrompt();

    // ===== Window buttons =====
    function setWindowState(state) {
        if (!windowEl) {
            return;
        }
        windowEl.classList.toggle('is-minimized', state === 'minimized');
        windowEl.classList.toggle('is-maximized', state === 'maximized');
        if (state !== 'minimized') {
            fitAddon.fit();
            term.focus();
        }
    }

    if (windowEl) {
        windowEl.addEventListener('click', (e) => {
            const button = e.target.closest('[data-terminal-action]');
            if (!button) {
                return;
            }
            const minimized = windowEl.classList.contains('is-minimized');
            const maximized = windowEl.classList.contains('is-maximized');
            switch (button.dataset.terminalAction) {
                case 'close':
                    setWindowState('normal');
                    endSession();
                    break;
                case 'minimize':
                    setWindowState(minimized ? 'normal' : 'minimized');
                    break;
                case 'maximize':
                    setWindowState(maximized && !minimized ? 'normal' : 'maximized');
                    break;
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && windowEl.classList.contains('is-maximized')) {
                setWindowState('normal');
            }
        });
    }

    // Auto-resize
    window.addEventListener('resize', () => {
        fitAddon.fit();
    });
}

// Export for use in Livewire
window.initTerminal = initTerminal;
