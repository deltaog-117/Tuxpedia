---
title: Init
author: Tuxpedia contributors
kind: concept
---
**Init** is the first process the [[kernel|kernel]] starts at boot, with process ID 1. It brings the rest of the system up: it mounts filesystems, starts background services (called daemons) and, when you shut down, stops them again. Every other process descends from it, and it adopts any process whose parent exits.

### Common init systems

- **systemd**: the default on most large distributions, including [[debian|Debian]], [[ubuntu|Ubuntu]] and [[fedora|Fedora]]. It starts services in parallel and also manages logging, timers and more, which some users consider too much for an init.
- **OpenRC**: a dependency-based init that works with ordinary shell scripts. It is used by [[alpine-linux|Alpine Linux]] and [[gentoo|Gentoo]], and is one of the choices on [[artix|Artix]].
- **runit**: a very small supervision suite, used by [[void-linux|Void Linux]]. A service is a directory holding a short run script.

### Why it matters

The init system decides how you enable a service at boot, read its logs and write your own services, so it shapes daily administration more than most people expect. It is also the main reason some distributions, such as [[artix|Artix]], exist.
