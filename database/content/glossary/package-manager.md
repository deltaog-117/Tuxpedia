---
title: Package manager
author: Tuxpedia contributors
kind: concept
---
A **package manager** installs, updates and removes software on a distribution. Software is shipped as **packages**: archives that hold the program's files together with metadata such as its version and the other packages it needs (its dependencies). The package manager downloads packages from repositories, resolves dependencies, checks signatures and keeps a database of what is installed.

### Common package managers

- `apt`: [[debian|Debian]], [[ubuntu|Ubuntu]] and [[linux-mint|Linux Mint]].
- `dnf`: [[fedora|Fedora]].
- `pacman`: [[arch-linux|Arch Linux]] and [[artix|Artix]].
- `apk`: [[alpine-linux|Alpine Linux]].
- `xbps`: [[void-linux|Void Linux]].
- `zypper`: [[opensuse|openSUSE]].
- `emerge` (Portage): [[gentoo|Gentoo]], which builds packages from source.
- `nix`: [[nix-os|NixOS]], which keeps every package version side by side.

### Why it matters

The package manager is the part of a distribution you touch most. It decides how fresh the software is, how upgrades roll back, and how much of the system you can reproduce on another machine. Switching distributions mostly means learning a new package manager.
